<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 */

namespace Zend\Search\Lucene;

use Zend\Search\Lucene\Storage\Directory\DirectoryInterface as Directory;
use Zend\Search\Lucene\Exception\RuntimeException;

/**
 * This is an utility class which provides index locks processing functionality
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 */
class LockManager
{
    /**
     * consts for name of file to show lock status
     */
    const WRITE_LOCK_FILE                = 'write.lock.file';
    const READ_LOCK_FILE                 = 'read.lock.file';
    const READ_LOCK_PROCESSING_LOCK_FILE = 'read-lock-processing.lock.file';
    const OPTIMIZATION_LOCK_FILE         = 'optimization.lock.file';

    /**
     * Obtain exclusive write lock on the index
     *
     * @param \Zend\Search\Lucene\Storage\Directory $lockDirectory
     * @return \Zend\Search\Lucene\Storage\File\FileInterface
     * @throws \Zend\Search\Lucene\Exception\RuntimeException
     */
    public static function obtainWriteLock(Directory $lockDirectory)
    {
        $lock = $lockDirectory->createFile(self::WRITE_LOCK_FILE);
        if (!$lock->lock(LOCK_EX)) {
            throw new RuntimeException('Can\'t obtain exclusive index lock');
        }
        return $lock;
    }

    /**
     * Release exclusive write lock
     *
     * @param \Zend\Search\Lucene\Storage\Directory $lockDirectory
     */
    public static function releaseWriteLock(Directory $lockDirectory)
    {
        $lock = $lockDirectory->getFileObject(self::WRITE_LOCK_FILE);
        $lock->unlock();
    }

    /**
     * Obtain the exclusive "read escalation/de-escalation" lock
     *
     * Required to protect the escalate/de-escalate read lock process
     * on GFS (and potentially other) mounted filesystems.
     *
     * Why we need this:
     *  While GFS supports cluster-wide locking via flock(), it's
     *  implementation isn't quite what it should be.  The locking
     *  semantics that work consistently on a local filesystem tend to
     *  fail on GFS mounted filesystems.  This appears to be a design defect
     *  in the implementation of GFS.  How this manifests itself is that
     *  conditional promotion of a shared lock to exclusive will always
     *  fail, lock release requests are honored but not immediately
     *  processed (causing erratic failures of subsequent conditional
     *  requests) and the releasing of the exclusive lock before the
     *  shared lock is set when a lock is demoted (which can open a window
     *  of opportunity for another process to gain an exclusive lock when
     *  it shoudln't be allowed to).
     *
     * @param \Zend\Search\Lucene\Storage\Directory $lockDirectory
     * @return \Zend\Search\Lucene\Storage\File\FileInterface
     * @throws \Zend\Search\Lucene\Exception\RuntimeException
     */
    private static function _startReadLockProcessing(Directory $lockDirectory)
    {
        $lock = $lockDirectory->createFile(self::READ_LOCK_PROCESSING_LOCK_FILE);
        if (!$lock->lock(LOCK_EX)) {
            throw new RuntimeException('Can\'t obtain exclusive lock for the read lock processing file');
        }
        return $lock;
    }

    /**
     * Release the exclusive "read escalation/de-escalation" lock
     *
     * Required to protect the escalate/de-escalate read lock process
     * on GFS (and potentially other) mounted filesystems.
     *
     * @param \Zend\Search\Lucene\Storage\Directory $lockDirectory
     */
    private static function _stopReadLockProcessing(Directory $lockDirectory)
    {
        $lock = $lockDirectory->getFileObject(self::READ_LOCK_PROCESSING_LOCK_FILE);
        $lock->unlock();
    }


    /**
     * Obtain shared read lock on the index
     *
     * It doesn't block other read or update processes, but prevent index from the premature cleaning-up
     *
     * @param \Zend\Search\Lucene\Storage\Directory $defaultLockDirectory
     * @return \Zend\Search\Lucene\Storage\File\FileInterface
     * @throws \Zend\Search\Lucene\Exception\RuntimeException
     */
    public static function obtainReadLock(Directory $lockDirectory)
    {
        $lock = $lockDirectory->createFile(self::READ_LOCK_FILE);
        if (!$lock->lock(LOCK_SH)) {
            throw new RuntimeException('Can\'t obtain shared reading index lock');
        }
        return $lock;
    }

    /**
     * Release shared read lock
     *
     * @param \Zend\Search\Lucene\Storage\Directory $lockDirectory
     */
    public static function releaseReadLock(Directory $lockDirectory)
    {
        $lock = $lockDirectory->getFileObject(self::READ_LOCK_FILE);
        $lock->unlock();
    }

    /**
     * Escalate Read lock to exclusive level
     *
     * @param \Zend\Search\Lucene\Storage\Directory $lockDirectory
     * @return boolean
     */
    public static function escalateReadLock(Directory $lockDirectory)
    {
        self::_startReadLockProcessing($lockDirectory);

        $lock = $lockDirectory->getFileObject(self::READ_LOCK_FILE);

        // First, release the shared lock for the benefit of GFS since
        // it will fail the conditional request to promote the lock to
        // "exclusive" while the shared lock is held (even when we are
        // the only holder).
        $lock->unlock();

        // GFS is really poor.  While the above "unlock" returns, GFS
        // doesn't clean up it's tables right away (which will potentially
        // cause the conditional locking for the "exclusive" lock to fail.
        // We will retry the conditional lock request several times on a
        // failure to get past this.  The performance hit is negligible
        // in the grand scheme of things and only will occur with GFS
        // filesystems or if another local process has the shared lock
        // on local filesystems.
        for ($retries = 0; $retries < 10; $retries++) {
            if ($lock->lock(LOCK_EX, true)) {
                // Exclusive lock is obtained!
                self::_stopReadLockProcessing($lockDirectory);
                return true;
            }

            // wait 1 microsecond
            usleep(1);
        }

        // Restore lock state
        $lock->lock(LOCK_SH);

        self::_stopReadLockProcessing($lockDirectory);
        return false;
    }

    /**
     * De-escalate Read lock to shared level
     *
     * @param \Zend\Search\Lucene\Storage\Directory $lockDirectory
     */
    public static function deEscalateReadLock(Directory $lockDirectory)
    {
        $lock = $lockDirectory->getFileObject(self::READ_LOCK_FILE);
        $lock->lock(LOCK_SH);
    }

    /**
     * Obtain exclusive optimization lock on the index
     *
     * Returns lock object on success and false otherwise (doesn't block execution)
     *
     * @param \Zend\Search\Lucene\Storage\Directory $lockDirectory
     * @return mixed
     */
    public static function obtainOptimizationLock(Directory $lockDirectory)
    {
        $lock = $lockDirectory->createFile(self::OPTIMIZATION_LOCK_FILE);
        if (!$lock->lock(LOCK_EX, true)) {
            return false;
        }
        return $lock;
    }

    /**
     * Release exclusive optimization lock
     *
     * @param \Zend\Search\Lucene\Storage\Directory $lockDirectory
     */
    public static function releaseOptimizationLock(Directory $lockDirectory)
    {
        $lock = $lockDirectory->getFileObject(self::OPTIMIZATION_LOCK_FILE);
        $lock->unlock();
    }
}

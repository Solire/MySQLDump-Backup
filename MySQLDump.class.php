<?php
namespace PH7\SQL\Backup;

/**
 * @class MySQLDump Class
 * @access public
 *
 * Backs up a database, creating a file for each day of the week,
 * using the mysqldump utility.
 * Can compress backup file with gzip of bzip2.
 * Intended for command line execution in conjunction with cron (e.g. cPanel, Plesk, ...).
 * Requires the user executing the script has permission to execute mysqldump.
 *
 * == Example of using this class ==
 *
 * - code -
 *
 * $oMySQLDump = new \PH7\SQL\Backup\MySQL('localhost', 'database_user', 'database_password', 'database_name', '/var/www/backup/sql/', 'bz2');
 * $oMySQLDump->backup();
 *
 * -- code --
 *
 * @use Please run your php script using this class (~/MySQLDump.class.php) with a cron (e.g. cPanel, Plesk, ...) and execute this script at a time periodic.
 *
 * @author      Pierre-Henry Soria <pierrehs@hotmail.com>
 * @link        http://github.com/pH-7
 * @copyright   Pierre-Henry Soria, All Rights Reserved.
 * @license     Lesser General Public License (LGPL) (http://www.gnu.org/copyleft/lesser.html)
 * @version     $Id: MySQLDump.class.php 1.2
 * @create      2012-02-13
 * @update      2012-05-28
 */
class MySQL
{

    /**
     * The backup command to execute.
     *
     * @access private
     * @var string $_sCmd
     */
    private $_sCmd;
    
    /**
     * The backup name file.
     *
     * @access private
     * @var string $_sFileName
     */
    private $_sFileName;

    /**
     * The extension allowed.
     *
     * @access private
     * @var array $_aZipExt
     */
    static private $_aZipExt = array('gz' => 'gzip', 'bz2' => 'bzip2');

    /**
     * MySQLDump constructor.
     *
     * @access public
     * @param string $sDbHost MySQL Host Name
     * @param string $sDbUser MySQL User Name
     * @param string $sDbPass MySQL User Password
     * @param string $sDbName Database to select
     * @param string $sDest The path to folder where the file will be stored backup
     * @param string $sZip Zip type; gz - gzip [default], bz2 - bzip
     * @return void
     */
    public function __construct($sDbHost, $sDbUser, $sDbPass, $sDbName, $sDest, $sZip = 'gz')
    {
        $bZip = (array_key_exists($sZip, self::$_aZipExt)) ? true : false;
        $sExt = ($bZip) ? '.' . $sZip : '';
        $this->_sFileName = 'Periodic-database-update.' . date('Y-m-d') . '.sql' . $sExt;
        $sOptions = ($bZip) ? ' | ' . self::$_aZipExt[$sZip] : '';

        $this->_sCmd = 'mysqldump -h' . $sDbHost . ' -u' . $sDbUser . ' -p' . $sDbPass . ' ' . $sDbName . $sOptions . ' > ' . $sDest . $this->_sFileName;
    }

    /**
     * Runs the constructed command.
     *
     * @access public
     * @return void
     */
    public function backup()
    {
        $sError = '';
        exec($this->_sCmd, $aOutput, $sError);
        return $sError == 0;
    }
    
    /**
     * Check if mysqldump is installed
     * 
     * @return boolean
     */
    static function mysqldumpIsInstalled()
    {
        // Test Mysqldump
        exec("which mysqldump", $aOutput, $sError);
        return $sError == 0;
    }
    
    /**
     * Check if command for compression is installed
     * 
     * @return boolean
     */
    static public function zipIsInstalled($sZip = 'gz')
    {
        // Test Mysqldump
        exec("which " . self::$_aZipExt[$sZip], $aOutput, $sError);
        return $sError == 0;
    }

    /**
     * Return current name of backup file.
     * 
     * @return string
     */
    public function get_sFileName() {
        return $this->_sFileName;
    }



}

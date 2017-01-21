<?php namespace prezire;
final class Config
{
    /**
     * [$timezone description]
     * @var string
     */
    public $timezone = 'Asia/Manila';
    
    /**
     * [$appUsername description]
     * @var string
     */
    public $appUsername = '285ec0eea2501591dda0e19dfed9e3af01fb84a1c9ce4dffcaab6dafbfe83967';
    
    /**
     * [$appPassword description]
     * @var string
     */
    public $appPassword = 'c291c72ada1c19af7af81cff65851cf69dfd0ffb2100a0821649fe5d8702bd76';

    /**
     * [$dbName description]
     * @var string
     */
    public $dbName = 'test';
    
    /**
     * [$dbHostName description]
     * @var string
     */
    public $dbHostName = 'localhost';
    
    /**
     * [$dbUsername description]
     * @var string
     */
    public $dbUsername = 'root';
    
    /**
     * [$dbPassword description]
     * @var string
     */
    public $dbPassword = '';
    
    /**
     * [$tblUniqueColIdent description]
     * @var string
     */
    public $tblUniqueColIdent = 'date_time_updated';

    
    /**
     * [$projDir description]
     * @var string
     */
    public $projDir = '/xampp/htdocs/tests/db_dump_manager/dump_files';
    
    /**
     * [$gitExecPath description]
     * @var string
     */
    public $gitExecPath = '/cygwin64/bin';
    
    /**
     * [$mySqlDumpExecPath description]
     * @var string
     */
    public $mySqlDumpExecPath = '/xampp/mysql/bin';

    
    /**
     * [$intervalType description]
     * @var string
     */
    public $intervalType = '1 DAY';
    
    /**
     * [$tableNames description]
     * @var array
     */
    public $tableNames = array('dealers');
    
    /**
     * [$repoBranch description]
     * @var string
     */
    public $repoBranch = 'task-10199';
}
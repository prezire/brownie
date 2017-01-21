<?php namespace prezire;
require_once 'prezire/DumpFileException.php';
final class MySqlBackupManager
{
  /**
   * [$tblNames description]
   * @var [type]
   */
  private $tblNames;

  /**
   * [$projDir description]
   * @var [type]
   */
  private $projDir;

  /**
   * [$dbProps description]
   * @var [type]
   */
  private $dbProps;

  /**
   * [$tblUniqueColIdent description]
   * @var [type]
   */
  private $tblUniqueColIdent;

  /**
   * [$mySqlDumpExecPath description]
   * @var [type]
   */
  private $mySqlDumpExecPath;

  /**
   * [$gitExecPath description]
   * @var [type]
   */
  private $gitExecPath;

  /**
   * [__construct description]
   * @param [type] $appUsername [description]
   * @param [type] $appPassword [description]
   */
  public function __construct($appUsername, $appPassword)
  {
    $c = new \prezire\Config();
    if
    (
      $appUsername !== $c->appUsername || 
      $appPassword !== $c->appPassword
    )
    {
      throw new \Exception('Invalid application credentials.');
    }
    echo 'Start backup.<br /><br />';
  }

  /**
   * [__destruct description]
   */
  public function __destruct()
  {
    echo '<br />End backup.';
  }

  /**
   * Look for errors on the first line on the dumped file.
   * [checkDumpedFileData description]
   * @param  [type] $file [description]
   * @return [type]       [description]
   */
  private function checkDumpedFileData($file)
  {
    $err = "Couldn't execute";
    $f = fopen($file, 'r');
    $line = fgets($f);
    fclose($f);
    $i = strpos($line, $err);
    if($i !== false)
    {
      //An error was found.
      //Reset any prev Git ops.
      $this->pushRepo(null, false);
      //Auto-exit esp to prevent executing any future Git ops.
      throw new \prezire\DumpFileException($line);
    }
  }
  
  /**
   * [createFile description]
   * @param  [type] $filename [description]
   * @return [type]           [description]
   */
  private function createFile($filename)
  {
    $projDir = $this->getProjDir();
    $fullFilename = "{$projDir}/db_dump_data_{$filename}.sql";
    $file = fopen($fullFilename, 'w');
    fclose($file);
    return realpath($fullFilename);
  }

  /**
   * [execSystem description]
   * @param  [type] $msg     [description]
   * @param  [type] $command [description]
   * @return [type]          [description]
   */
  private function execSystem($msg, $command)
  {
    echo $msg, system($command), '.<br />';
  }

  /**
   * [setTableUniqueColIdent description]
   * @param [type] $identName [description]
   */
  public function setTableUniqueColIdent($identName)
  {
    $this->tblUniqueColIdent = $identName;
  }
  
  /**
   * [getTableUniqueColIdent description]
   * @return [type] [description]
   */
  public function getTableUniqueColIdent(){return $this->tblUniqueColIdent;}

  /**
   * [setGitExecPath description]
   * @param [type] $path [description]
   */
  public function setGitExecPath($path){$this->gitExecPath = $path;}
  
  /**
   * [getGitExecPath description]
   * @return [type] [description]
   */
  public function getGitExecPath(){return $this->gitExecPath;}

  /**
   * [setMySqlDumpExecPath description]
   * @param [type] $path [description]
   */
  public function setMySqlDumpExecPath($path){$this->mySqlDumpExecPath = $path;}
  
  /**
   * [getMySqlDumpExecPath description]
   * @return [type] [description]
   */
  public function getMySqlDumpExecPath(){return $this->mySqlDumpExecPath;}

  /**
   * [setProjDir description]
   * @param [type] $dir [description]
   */
  public function setProjDir($dir){$this->projDir = $dir;}
  
  /**
   * [getProjDir description]
   * @return [type] [description]
   */
  public function getProjDir(){return $this->projDir;}

  /**
   * [setTableNames description]
   * @param array $names [description]
   */
  public function setTableNames(array $names){$this->tblNames = $names;}
  
  /**
   * [getTableNames description]
   * @return [type] [description]
   */
  public function getTableNames(){return $this->tblNames;}

  /**
   * [setDbProps description]
   * @param [type] $hostName [description]
   * @param [type] $dbName   [description]
   * @param [type] $username [description]
   * @param [type] $password [description]
   */
  public function setDbProps($hostName, $dbName, $username, $password)
  {
    $this->dbProps = array
    (
      'hostName' => $hostName,
      'dbName' => $dbName,
      'username' => $username,
      'password' => $password
    );
  }
  
  /**
   * [getDbProps description]
   * @return [type] [description]
   */
  public function getDbProps(){return $this->dbProps;}

  /**
   * [backup description]
   * @param  [type] $intervalType [description]
   * @return [type]               [description]
   */
  public function backup($intervalType)
  {
    $tblNames = $this->getTableNames();
    $dbProps = $this->getDbProps();
    $dbName = $dbProps['dbName'];
    $username = $dbProps['username'];
    $password = $dbProps['password'];
    $uniqueColIdent = $this->getTableUniqueColIdent();
    $mySqlDumpPath = $this->getMySqlDumpExecPath() . '/mysqldump';
    foreach ($tblNames as $tbl)
    {
      //Append table name as a file's name.
      $file = $this->createFile($tbl);
      //
      $sPwd = empty($password) ? '' : "-p{$password}";
      $sDump = "{$mySqlDumpPath} --skip-add-drop-table --no-create-info --skip-comments -u {$username} {$sPwd} {$dbName} {$tbl} --where=\"{$uniqueColIdent} > DATE_SUB(NOW(), INTERVAL {$intervalType})\" > {$file} 2>&1";
      $this->execSystem("Dumping: {$sDump}", $sDump);
      $this->checkDumpedFileData($file);
      echo '<br />';
    }
  }

  /**
   * [pushRepo description]
   * @param  [type]  $branch  [description]
   * @param  boolean $success [description]
   * @return [type]           [description]
   */
  public function pushRepo($branch, $success = true)
  {
    $projDir = $this->getProjDir();
    chdir($projDir);
    //Only perform git ops if there's a .git folder.
    if(file_exists($projDir . '/.git'))
    {
      //
      if($success === true)
      {
        $sGit = $this->getGitExecPath() . '/git';
        $date = date('YmdHis');
        $this->execSystem('Repo add: ', "{$sGit} add .");
        $this->execSystem('Repo commit: ', "{$sGit} commit -m 'Incremental DB data backup {$date}.'");
        $this->execSystem('Repo push: ', "{$sGit} push -u origin {$branch} 2>&1");
      }
      else
      {
        $this->execSystem('Repo reset: ', "{$sGit} reset 2>&1"); 
      }
    }
  }
}
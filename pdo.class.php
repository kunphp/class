<?php
/**
 * PDO操作类
 * 说明 : 如果要使用 getAllWithPage()方法则必须要引入分页类
 *
 */

class pdoModel{
    
    //pdo对象
    private $_pdo;

    //数据库名称
    private $_name;

    //最后执行的sql
    protected $_lastExecSql;

    //最后插入的ID
    protected $_lastInsertId;



    /**
     * 根据数据库配置参数 初始化PDO对象
     * 
     * @param    array    $DB_CONFIG    数据库配置参数
     * @return   object   $pdo          返回实例化后的PDO对象
     */
    function __construct( $DB_CONFIG = array() ) {

        if ( empty($DB_CONFIG) ) {
            echo 'ERROR: DB_CONFIG array is empty';
            die();
        }

        //拼接字符串
        $pdoDSN = '';
        $pdoDSN = "mysql:dbname={$DB_CONFIG['DB_NAME']};host={$DB_CONFIG['DB_HOST']};port={$DB_CONFIG['DB_PORT']};";

        //记录数据库名称
        $_name = $DB_CONFIG['DB_NAME'];

        try {

            $pdo = NULL;

            //实例化PDO对象
            $pdo = new PDO($pdoDSN, $DB_CONFIG['DB_USER'], $DB_CONFIG['DB_PWD'], $DB_CONFIG['DB_OPTIONS']);

            //设置数据库编码
            $pdo->exec(" SET NAMES '{$DB_CONFIG['DB_CHARSET']}' ");

            $this->_pdo = $pdo;
        } catch (PDOException $e) {
            echo 'Init PDO ERROR: ' . $e->getMessage();
            die();
        }
    }



    /**
     * 获取pdo对象
     *
     * @return    Object    $pdo    获取pdo对象
     */
    public function getPDOInstance(){
        return $this->_pdo;
    }



    /**
     * 获取最后新增的id
     * 
     * @return    integer    $id    获取最后新增的id
     */
    public function lastInsertId(){
        return $this->_pdo->lastInsertId();
    }



    /**
     * 获取最后执行的sql语句
     * 
     * @return string    $sql    最后执行的sql语句
     */
    public function getLastSql(){
        return $this->_lastExecSql;
    }



    /**
     * 带分页的查询
     * 
     * @param     string     $searchSql    查询sql语句
     * @param     integer    $rows         每页显示条数
     * @param     string     $params       额外附加参数,例如 : &uid=1001 
     * @param     boolean    $debug        是否开启调试模式，开始调试模式直接返回sql
     * @return    array      $result       返回结果
     */
    public function getAllWithPage($searchSql = '' , $rows = 10 , $params = '' , $debug = false ){
        $debugInfo = array();

        $result = array();

        if( empty($searchSql) ){
            return $result;
        }
        
        $total = 0;

        $total = $this->getCount($searchSql);

        if( intval($total) <= 0 ){
            return $result;
        }
        
        //加载分页类
        require_once __DIR__.DIRECTORY_SEPARATOR.'pageModel.class.php';

        //实例化分页类
        $pageModel = new pageModel($total , $rows , $params);

        $searchPageSql = $searchSql . $pageModel->limit;

        //开启调试模式
        if( true === $debug ){

            $debugInfo['searchSql'] = $searchSql;

            $debugInfo['listSql'] = $searchPageSql;
            
            //显示分页信息
            $debugInfo['pageStr'] = $pageModel->showPage();

            return $debugInfo;
        }

        $list = array();

        $list = $this->getAll($searchPageSql);

        $result['total'] = $total;

        $result['pageStr'] = $pageModel->showPage();

        $result['list'] = $list;

        return $result;
    }    




    /**
     * 自动替换查询条件中的统计语句
     * 
     * @param     string     $searchSql    查询sql语句
     * @param     boolean    $debug        是否开启调试模式
     * @return    integer    $count        查询数量
     */
    public function getCount($searchSql = '' , $debug = false ){

        if( empty($searchSql) ) {
            return 0;
        }

        try{
            //检测 最后一个 FROM 所在字符串中位置 
            $fromStrPosition = strripos( trim($searchSql) ,' FROM ');

            if( intval($fromStrPosition) <= 0 ){
                return 0;
            }

            //替换最后一个 FROM 之前的查询语句
            $countSqlStr = substr_replace( trim($searchSql) , ' SELECT count(*) AS count FROM ', 0 , $fromStrPosition + 6 );

            $count = $this->getColumn($countSqlStr,$debug);

            return intval($count);

        } catch (Exception $e){
            echo " GET COUNT ERROR : ".$e->getMessage();
            die();
        }
        
    }    


    /**
     * 自动替换查询条件中的统计语句
     * 
     * @param     string     $searchSql    查询sql语句
     * @param     boolean    $debug        是否开启调试模式
     * @return    integer    $count        查询数量
     */
    public function getCount2($searchSql = '' , $debug = false ){

        if( empty($searchSql) ) {
            return 0;
        }

        try{
/*            //检测 最后一个 FROM 所在字符串中位置 
            $fromStrPosition = strripos( trim($searchSql) ,' FROM ');

            if( intval($fromStrPosition) <= 0 ){
                return 0;
            }

            //替换最后一个 FROM 之前的查询语句
            $countSqlStr = substr_replace( trim($searchSql) , ' SELECT count(*) AS count FROM ',0 , $fromStrPosition + 6);*/

            $count = $this->getColumn($searchSql,$debug);

            return intval($count);

        } catch (Exception $e){
            echo " GET COUNT ERROR : ".$e->getMessage();
            die();
        }
        
    }



    /**
     * 查询所有记录
     * 
     * @param     string     $searchSql    sql查询语句
     * @param     boolean    $debug        是否开启调试模式，开始调试模式直接返回sql
     * @return    array      $result       查询结果
     */
    public function getAll( $searchSql = '' , $debug = false){

        if( empty($searchSql) ) {
            return NULL;
        }

        if(true === $debug) {
            return $searchSql;
        }

        try {
    
            //执行sql预处理
            $sth = $this->_pdo->prepare($searchSql);

            $execResult = $sth->execute();

            if( false === $execResult ){
                return NULL;
            }
    
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);

            $this->_lastExecSql = $searchSql;
    
            return $result;
            
        } catch (Exception $e) {
            //捕获异常信息
            echo " GET ALL ERROR : ".$e->getMessage();
            die();
        }
    }



    /**
     * 查询单条记录
     * 
     * @param     string     $searchSql    sql查询语句
     * @param     boolean    $debug        是否开启调试模式，开始调试模式直接返回sql
     * @return    array      $result       查询结果
     */
    public function getRow( $searchSql = '' , $debug = false){

        if( empty($searchSql) ) {
            return NULL;
        }

        if(true === $debug) {
            return $searchSql;
        }

        try {
    
            //执行sql预处理
            $sth = $this->_pdo->prepare($searchSql);

            $execResult = $sth->execute();

            if( false === $execResult ){
                return NULL;
            }
    
            $result = $sth->fetch(PDO::FETCH_ASSOC);

            $this->_lastExecSql = $searchSql;
    
            return $result;
            
        } catch (Exception $e) {
            //捕获异常信息
            echo " GET ROW ERROR : ".$e->getMessage();
            die();
        }
    }




    /**
     * 查询单个字段数值
     * 
     * @param     string     $searchSql    sql查询语句
     * @param     boolean    $debug        是否开启调试模式，开始调试模式直接返回sql
     * @return    mixed      $result       查询结果
     */
    public function getColumn( $searchSql = '' , $debug = false){

        if( empty($searchSql) ) {
            return NULL;
        }

        if(true === $debug) {
            return $searchSql;
        }

        try {
    
            //执行sql预处理
            $sth = $this->_pdo->prepare($searchSql);

            $execResult = $sth->execute();

            if( false === $execResult ){
                return NULL;
            }
    
            $result = $sth->fetchColumn();

            $this->_lastExecSql = $searchSql;
    
            return $result;
            
        } catch (Exception $e) {
            //捕获异常信息
            echo " GET COLUMN ERROR : ".$e->getMessage();
            die();
        }
    }




    /**
     * 新增数据
     * 
     * @param     string     $tableName       数据库表名称
     * @param     array      $insertDataArr   新增数据数组
     * @param     boolean    $debug           是否开启调试模式，开始调试模式直接返回sql
     * @return    integer    $rows            返回影响记录的行数，如果发生错误返回false
     */
    public function insert($tableName = '' , $insertDataArr = array() , $debug = false){
    
        //如果表名或者插入数据为空，则直接返回0
        if( empty($tableName) || empty($insertDataArr) ) {
            return false;
        }
        
        //开启debug调试模式，则直接输出sql语句
        if(true === $debug){

            $debugSql = "INSERT INTO `$tableName` (`".implode('`,`', array_keys($insertDataArr))."`) VALUES ('".implode("','", $insertDataArr)."')";
            
            return $debugSql;
        }

        $bindValueStr = '';

        foreach ( array_keys($insertDataArr) as $k => $val) {
            
            if( $k > 0 ){
                $bindValueStr .= ", :$val ";
            }else{
                $bindValueStr .= " :$val ";
            }
        }

        $insertSql = '';

        $insertSql = "INSERT INTO `{$tableName}` (`".implode('`,`', array_keys($insertDataArr) )."`) VALUES ( $bindValueStr ) ";
    
        try {

            //预处理sql
            $sth = $this->_pdo->prepare($insertSql);

            foreach ($insertDataArr as $key => &$value) {
                $sth->bindParam(":$key", $value);
            }

            //执行sql语句
            $execResult = $sth->execute();
            
            // 预留错误调试 prepare
            // $debugInfo = $sth->debugDumpParams();

            if($execResult == false) {
                return false;
            }

            //获取受影响行数
            $rows = $sth->rowCount();

            //获取新增的id
            $insertId = $this->_pdo->lastInsertId();

            $this->_lastInsertId = $insertId;

            //获取最后执行的sql
            $this->_lastExecSql = $insertSql;
            
            return $rows;
        } catch (Exception $e) {
            //捕获异常信息
            echo " EXEC ERROR : ".$e->getMessage();
            die();
        }
    }    



    /**
     * 新增或者数据
     * 
     * @param     string     $tableName       数据库表名称
     * @param     array      $insertDataArr   新增数据数组
     * @param     boolean    $debug           是否开启调试模式，开始调试模式直接返回sql
     * @return    integer    $rows            返回影响记录的行数，如果发生错误返回false
     */
    public function replace($tableName = '' , $insertDataArr = array() , $debug = false){
    
        //如果表名或者插入数据为空，则直接返回0
        if( empty($tableName) || empty($insertDataArr) ) {
            return false;
        }
        
        //开启debug调试模式，则直接输出sql语句
        if(true === $debug){

            $debugSql = "REPLACE INTO `$tableName` (`".implode('`,`', array_keys($insertDataArr))."`) VALUES ('".implode("','", $insertDataArr)."')";
            
            return $debugSql;
        }

        $bindValueStr = '';

        foreach ( array_keys($insertDataArr) as $k => $val) {
            
            if( $k > 0 ){
                $bindValueStr .= ", :$val ";
            }else{
                $bindValueStr .= " :$val ";
            }
        }

        $insertSql = '';

        $insertSql = "REPLACE INTO `{$tableName}` (`".implode('`,`', array_keys($insertDataArr) )."`) VALUES ( $bindValueStr ) ";
    
        try {

            //预处理sql
            $sth = $this->_pdo->prepare($insertSql);

            foreach ($insertDataArr as $key => &$value) {
                $sth->bindParam(":$key", $value);
            }

            //执行sql语句
            $execResult = $sth->execute();
            
            // 预留错误调试 prepare
            // $debugInfo = $sth->debugDumpParams();

            if($execResult == false) {
                return false;
            }

            //获取受影响行数
            $rows = $sth->rowCount();

            
            return $rows;
        } catch (Exception $e) {
            //捕获异常信息
            echo " EXEC ERROR : ".$e->getMessage();
            die();
        }
    }



    /**
     * 更新数据
     * 
     * @param     string     $tableName        数据库表名称
     * @param     array      $updateDataArr    新增数据数组
     * @param     string     $whereSql         where限制语句，不能为空(防止误操作)，为空直接返回false
     * @param     boolean    $debug            是否开启调试模式，开始调试模式直接返回sql
     * @return    integer    $rows             返回影响记录的行数，如果发生错误返回false
     */
    public function update($tableName , $updateDataArr , $whereSql , $debug = false){

        if( empty($tableName) || empty($updateDataArr) || empty($whereSql) ){
            return false;
        }

        $updateSqlStr = '';

        foreach ($updateDataArr as $key => $value) {
            $updateSqlStr .= ", `$key`='$value'";
        }

        $updateSqlStr = substr($updateSqlStr, 1);

        $updateSqlStr = "UPDATE `$tableName` SET $updateSqlStr WHERE $whereSql";

        $result = $this->exec($updateSqlStr,$debug);

        return $result;
    }    



    /**
     * 更新数据
     * 
     * @param     string     $tableName        数据库表名称
     * @param     array      $updateDataArr    新增数据数组
     * @param     string     $whereSql         where限制语句，不能为空(防止误操作)，为空直接返回false
     * @param     boolean    $debug            是否开启调试模式，开始调试模式直接返回sql
     * @return    integer    $rows             返回影响记录的行数，如果发生错误返回false
     */
    public function update2($tableName , $updateDataArr , $whereSql , $debug = false){

        if( empty($tableName) || empty($updateDataArr) || empty($whereSql) ){
            return false;
        }
        //开启debug调试模式，则直接输出sql语句
        if(true === $debug){
            $debugSql = '';
            foreach ($updateDataArr as $key => $value) {
                
                $debugSql .= ", $key = '$value' ";
            }

            $debugSql = substr($debugSql, 1);

            $debugSql = "UPDATE `$tableName` SET $debugSql WHERE $whereSql";

            return $debugSql;
        }

        $updateSqlStr = '';

        foreach ($updateDataArr as $key => $value) {
            
            $updateSqlStr .= ", $key = :$key ";
        }

        $updateSqlStr = substr($updateSqlStr, 1);

        $updateSqlStr = "UPDATE `$tableName` SET $updateSqlStr WHERE $whereSql";

        try {

            //预处理sql
            $sth = $this->_pdo->prepare($updateSqlStr);

            foreach ($updateDataArr as $key => &$value) {
                $sth->bindParam(":$key", $value);
            }

            //执行sql语句
            $execResult = $sth->execute();
            
            // 预留错误调试 prepare
            // $debugInfo = $sth->debugDumpParams();

            if($execResult == false) {
                return false;
            }

            //获取受影响行数
            $rows = $sth->rowCount();
            
            return $rows;
        } catch (Exception $e) {

            //捕获异常信息
            echo " EXEC ERROR : ".$e->getMessage();
            die();
        }

    }



    /**
     * 执行sql语句 （仅限于新增、修改、删除）
     * 
     * @param     string     $execSql    执行的sql语句
     * @param     boolean    $debug      是否开启调试模式，开始调试模式直接返回sql
     * @return    integer    $rows       返回操作受影响的行数
     */
    public function exec($execSql , $debug = false ){
        
        if( empty($execSql) ){
            return false;
        }

        if( true === $debug ){
            return $execSql;
        }

        try {

            //预处理sql
            $sth = $this->_pdo->prepare($execSql);

            $execResult = $sth->execute();

            if($execResult == false) {
                return false;
            }

            //获取受影响行数
            $rows = $sth->rowCount();

            //获取最后执行的sql
            $this->_lastExecSql = $execSql;

            return $rows;
            
        } catch (Exception $e) {
            //捕获异常信息
            echo " ERROR : ".$e->getMessage();
            die();
        }
    }



    /**
     * beginTransaction 事务开始
     */
    public function beginTransaction(){
        $this->_pdo->beginTransaction();
    }
    


    /**
     * commit 事务提交
     */
    public function commit(){
        $this->_pdo->commit();
    }
    


    /**
     * rollback 事务回滚
     */
    public function rollback(){
        $this->_pdo->rollback();
    }



    /**
     * 检测连接是否存在
     */
    public function checkPing(){

        try{
            $this->_pdo->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch (PDOException $e) {
            if( strpos($e->getMessage(), 'MySQL server has gone away') !== false ){
                return false;
            }
        }
        
        return true;
    }



    /**
     * 关闭PDO连接
     * 
     */
    public function close(){
        $this->_pdo = NULL;
        $this->_name = '';
        $this->_lastInsertId = 0;
        $this->_lastExecSql = '';
    }
}

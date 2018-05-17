<?php
/**
 * 分页类
 */


class pageModel{


    private $total;     //数据表中总记录数

    private $listRows;  //每页显示行数

    private $limit;     //查询限制字符串

    private $uri;       //自动获取url请求地址

    private $showNum;   //两侧显示的页码数

    private $pageNum;   //总页数

    private $page;      //当前页数

    private $config = array('header' => '条记录','prev' => '上一页','next' => '下一页','first' => '首页','last' => '尾页');



    /**
     * 构造函数
     * 
     * @param    integer    $total       总记录数
     * @param    integer    $listRows    每页显示条数，默认为10
     * @param    string     $params      额外附加参数,例如 : &uid=1001 
     * @param    integer    $showNum     两侧显示的页码数,默认为2
     */
    public function __construct($total = 0 , $listRows = 10 , $params = '' , $showNum = 2){

        $this->total = intval($total);

        $this->listRows = intval($listRows);

        $this->showNum = intval($showNum);

        $this->uri = $this->getUri($params);

        $this->page = 1;

        if( isset($_GET['page']) && intval($_GET['page']) > 0) {
            $this->page = intval($_GET['page']);
        } 

        $this->pageNum = ceil($this->total/$this->listRows);

        //超出范围，则为当前最大页
        if($this->page > $this->pageNum){
            $this->page = $this->pageNum;
        }
        
        $this->limit = $this->setLimit();
    }



    /**
     * 获取该类的私有属性
     * 
     * @param     string    $key       该类私有属性的变量
     * @return    mixed     $result    返回数值
     */
    public function __get( $key = '' ){

        if( !empty($key) && isset($this->$key) ){
            return $this->$key; 
        }else{
            return NULL;
        }
    }



    /**
     * 拼装mysql限制查询
     */
    private function setLimit(){
        return ' limit '.($this->page-1)*$this->listRows.','.$this->listRows;
    }



    /**
     * 获取 mongoDB 中分页需要的参数 skip
     */
    public function getSkip(){

        $currentPage = intval($this->page);

        if( $currentPage < 1 ){
            $currentPage = 1;
        }

        $skip = ($currentPage - 1) * $this->listRows;

        return $skip;
    }
    


    /**
     * 获取url
     * 
     * @param    string    $paramStr    额外附加参数
     * @return   string    $url         返回url
     */
    public function getUri($paramStr = ''){

        $url = $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$paramStr;
        
        //返回一个数组，数组有元素query和path
        $parse = parse_url($url);

        if( isset($parse['query']) ){

            //将query中的参数解析到$params，为数组
            parse_str($parse['query'],$params);

            unset($params['page']);

            //按照指定的参数生成一个请求字符串
            $url = $parse['path'].'?'.http_build_query($params);
        }

        return $url;
    }



    /**
     * 返回 首页链接
     * 
     * @return    string    $html    首页链接的html代码
     */
    private function first(){

        if( !isset($html) ) {
            $html = '';
        }

        if( $this->page == 1){
            $html.= '';
        }else{
            $html.= "&nbsp;<a class='pageModel page_first' href='{$this->uri}&page=1'>{$this->config['first']}</a>&nbsp;";
        }

        return $html;
    }
    


    /**
     * 返回上一页连接
     * 
     * @return    string    $html    上一页链接的html代码
     */
    private function prev(){

        if( !isset($html) ) {
            $html = '';
        }

        if($this->page == 1){
            $html.= '';
        }else{
            $html.= "&nbsp;<a class='pageModel page_prev' href='{$this->uri}&page=".($this->page-1)."'>{$this->config['prev']}</a>&nbsp;";
        }

        return $html;
    }



    /**
     * 分页中间页码显示
     * 
     * @return    string    $linkPage    返回 页码 的html代码
     */
    private function pagelist(){
        
        $linkPage = '';

        //每边显示iNum个页码
        $iNum = $this->showNum;

        //左边的页码
        for($i = $iNum; $i >= 1; $i--){

            $page = $this->page - $i;

            if( $page < 1 ){
                continue;
            }else{
                $linkPage .= "&nbsp;<a class='pageModel page_number' href='{$this->uri}&page={$page}'>{$page}</a>&nbsp;";
            }
        }

        //当前页码
        $linkPage .= "&nbsp;<span class='page_current'>{$this->page}</span>&nbsp;";

        //右边页码
        for($i = 1; $i <= $iNum; $i++){

            $page = $this->page + $i;

            if($page > $this->pageNum){
                break;
            }else{
                $linkPage.="&nbsp;<a class='pageModel page_number' href='{$this->uri}&page={$page}'>{$page}</a>&nbsp;";
            }
        }
        
        return $linkPage;
    }
    


    /**
     * 下一页
     * 
     * @return    string    $html    返回 下一页 的html代码
     */
    private function next(){

        if( !isset($html) ) {
            $html = '';
        }

        if($this->page == $this->pageNum){
            $html.= '';
        }else{
            $html.= "&nbsp;<a class='pageModel page_next' href='{$this->uri}&page=".($this->page+1)."'>{$this->config['next']}</a>&nbsp;";
        }

        return $html;
    }



    /**
     * 尾页
     * 
     * @return    string    $html    返回 尾页的html代码
     */
    private function last(){

        if( !isset($html) ) {
            $html = '';
        }

        if( $this->page == $this->pageNum ){
            $html.='';
        }else{
            $html.="&nbsp;<a class='pageModel page_last' href='{$this->uri}&page={$this->pageNum}'>{$this->config['last']}</a>&nbsp;";
        }
        return $html;
    }



    /**
     * 跳转页面html代码
     * 
     * @return    string    $html    返回跳转页面的html代码
     */
    private function goPage(){
        return '&nbsp;&nbsp;
        <input id="page_jump_input" class="page_jump_input" type="text" onkeydown="javascript:if(event.keyCode==13){
          var page=(this.value>'.$this->pageNum.')?'.$this->pageNum.':this.value;  location=\''.$this->uri.'&page=\'+page+\'\' }" 
          value="'.$this->page.'" style="width:25px" >
        <input type="button" value="GO" class="page_jump_button" onclick="javascript:var page=(document.getElementById(\'page_jump_input\').value>'.$this->pageNum.')?'.$this->pageNum.':document.getElementById(\'page_jump_input\').value; 
        location=\''.$this->uri.'&page=\'+page+\'\' ">&nbsp;&nbsp;';
    }



    /**
     * 显示分页输出
     * 
     * @param     array     $pageShowConfig    分页配置参数数组
     * @return    string    $pageStr           分页字符串
     */
    function showPage($pageShowConfig = array(0,1,2,3,4,5,6,7) ){

        $html[0] = "&nbsp;<span class='page_total_txt'>共有<b>{$this->total}</b>{$this->config['header']}</span>&nbsp;";
        $html[1] = "&nbsp;{$this->page}/{$this->pageNum}页&nbsp;";
        $html[2] = $this->first();
        $html[3] = $this->prev();
        $html[4] = $this->pageList();
        $html[5] = $this->next();
        $html[6] = $this->last();
        $html[7] = $this->goPage();

        $pageStr = '';

        foreach($pageShowConfig as $index){
            $pageStr .= $html[$index];
        }
        
        return $pageStr;
    }
}

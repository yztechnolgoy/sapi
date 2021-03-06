<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
//开启强类型
declare(strict_types=1);
namespace Common\Logic;

use Common\Tool\Extend\ArrayChildren;
use Common\Tool\Tool;
use Think\Hook;
use Common\Tool\Event;
use Common\Model\BaseModel;

/**
 * 逻辑处理层抽象类
 * @author 王强
 * @version 1.1.2
 */
abstract class AbstractGetDataLogic
{
    /**
     * 状态
     * @var int
     */
    private $status = 0;

    /**
     * 添加时报错
     * @var int
     */
    const ADD_ERROR = 0x00;

    // 数据
    protected $data = array();

    // guan表搜索条件
    private $associationWhere = [];

    /**
     * 搜索条件临时属性, 必须是数组
     */
    protected $searchTemporary = [];

    /**
     * 分割键
     * @var string
     */
    protected $splitKey = '';

    /**
     * 模型对象
     * @var BaseModel
     */
    protected $modelObj;

    /**
     * 错误消息
     * @var string
     */
    protected $errorMessage = '';

    /**
     * 时间临时缓存键  // 时间搜索键
     * @var string
     */
    private $timeGpKey = 'timegp';

    //数据验证
    const VALIDATE_MASTER_PATH = '\\PlugInUnit\\Validate\\Children\\';

    /**涉及到其他表搜索的字段（用关联字段替换）*/
    protected $covertKey = '';

    /**
     * 搜索标记 该条件是否有数据
     * @var boolean
     */
    private $whereExits = TRUE;

    /**
     * @return the $otherWhere
     */
    public function getAssociationWhere()
    {
        return $this->associationWhere;
    }

    /**
     * @return the $whereExits
     */
    public function getWhereExits()
    {
        return $this->whereExits;
    }

    /**
     *
     * @return the $errorMessage
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     *
     * @param string $splitKey
     */
    public function setSplitKey( $splitKey )
    {
        $this->splitKey = $splitKey;
    }

    /**
     * 设置关联条件
     * @param field_type $otherWhere
     */
    public function setAssociationWhere( $otherWhere )
    {
        $this->associationWhere = $otherWhere;
    }

    /**
     *
     * @return BaseModel
     */
    public function getModelObj()
    {
        return $this->modelObj;
    }

    /**
     *
     * @param field_type $modelObj
     */
    public function setModelObj( $modelObj )
    {
        $this->modelObj = $modelObj;
    }

    /**
     *
     * @return the $goodsData
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     *
     * @param multitype : $goodsData
     */
    public function setData( $goodsData )
    {
        $this->data = $goodsData;
    }

    /**
     * 回调方法【处理方法】关联字段相互替换
     */
    protected function parseDataWhere()
    {
        $data = $this->data;

        $data[$this->covertKey] = !isset($data[$this->splitKey]) ? $data[$this->covertKey] : $data[$this->splitKey];
        
        unset( $data[ $this->splitKey ]);
        
        return $data;
    }

    /**
     * 设置模糊查询字段
     */
    protected function likeSerachArray() :array
    {
        return [];
    }
	
    
    /**
     * 组装搜索条件
     *
     * @param boolean $isLike
     *            是否全部模糊搜索
     * @param array $likeSearch
     *            指定模糊搜索
     * @return array
     */
    public function getSearchBuildWhere() :array
    {
        if( !is_array( $this->data ) || empty( $this->data ) ){
            return array();
        }

        $data = $this->parseDataWhere();

        // 处理查询条件
        $orderBy = ( new ArrayChildren( $data ) )->buildActive();

        if( empty( $orderBy ) ){
            return array();
        }

        $where = $this->modelObj->create( $orderBy );
        $likeSearch = $this->likeSerachArray();
        
       
        if( empty( $likeSearch ) ){
            return $where;
        }
		
        $search = [];
        
        foreach( $likeSearch as $key => $value ){
            if( empty( $where[ $value ] ) ){
                continue;
            }

            $search[ $value ] = array(
                'like',
                $where[ $value ] . '%'
            );
        }
        
        return $search;
    }

    /**
     * 获取具体搜索字段（非模糊）
     * @return
     */
    protected  function searchArray() {
    	
    	return [];
    }
    
    /**
     * 组装搜索条件（匹配搜索）
     * @return array
     */
    protected function getMatchingSearchBuildWhere() :array
    {
    	if( empty($this->data)){
    		return [];
    	}
    	
    	$search = $this->searchArray();
    	
    	if( empty($search) ){
    		return [];
    	}
    	
    	// 处理查询条件
    	$data = ( new ArrayChildren( $this->data ) )->buildActive();
    	
    	$mapSearch = $this->modelObj->create($data);
    	
    	if( empty($data) ){
    		
    		return [];
    	}
    	
    	$lenth = count($search);
    	
    	$where = [];
    	
    	for( $i = 0; $i < $lenth; $i++){
    		
    		if( $mapSearch[$search[$i]]===null ){
    			continue;
    		}
    		$where[$search[$i]] = $mapSearch[$search[$i]];
    	}
    	
    	return $where;
    }
    
    /**
     * 获取搜索时间key
     */
    protected function getSearchTimeKey()
    {
        $model = &$this->modelObj;
        return $model::$createTime_d;
    }

    /**
     * 获取排序
     */
    protected function getSearchOrderKey()
    {
        $model = &$this->modelObj;
        return $model::$id_d . $model::DESC;
    }

    /**
     * 获取关联条件
     * @return array
     */
    public function getAssociationCondition()
    {
        $where = $this->getSearchBuildWhere();
        if( empty( $where ) ){
            return [];
        }

        $model = $this->modelObj;

        $data = $model->field( $model::$id_d )
            ->where( $where )
            ->select();
        if( empty( $data ) ){
            $this->whereExits = FALSE;

            return [];
        }

        $ids = Tool::characterJoin( $data,$model::$id_d );
        return [
            $this->splitKey => [
                'in',
                $ids
            ]
        ];
    }

    /**
     * 处理时间 搜索条件
     * @return array
     */
    protected function parseTimeWhere()
    {
        $key = $this->timeGpKey;

        if( empty( $this->data[ $key ] ) ){
            return [];
        }

        $timeParam = $this->data[ $key ];
        if( empty( $timeParam ) || false === strpos( $timeParam,' - ' ) ){
            return array();
        }

        list ( $startTime,$endTime ) = explode( ' - ',$timeParam );

        $startTime = strtotime( $startTime );

        $endTime = strtotime( $endTime );

        return [
            'between',
            [
                $startTime,
                $endTime
            ]
        ];
    }

    /**
     * @return the $timeGpKey
     */
    public function getTimeGpKey()
    {
        return $this->timeGpKey;
    }

    /**
     * 获取分页数据列表
     *
     * @return array
     */
    public function getDataList()
    {

        $options = $this->buildWhere();
       
        $model = $this->modelObj;
        $data = $this->getDataByPage( $options );
        if( empty( $data[ 'data' ] ) ){
            return [];
        }
        
        // 转换序号
        $data[ 'data' ] = ( new ArrayChildren( $data[ 'data' ] ) )->convertIdByData( $model::$id_d );
        
        $key = $this->getCacheKey();
        
        $_SESSION[$key] = $data[ 'data' ];

        return $data;
       
    }

    protected  function buildWhere()
    {
        // 店铺搜索条件
        $where = $this->getSearchBuildWhere();
        //匹配搜索条件
        $matchWhere = $this->getMatchingSearchBuildWhere();
        
        $where = array_merge( $where, $matchWhere, $this->associationWhere, $this->searchTemporary );
        if( !empty( $this->data[ $this->timeGpKey ] ) ){

            $timeWhere                              = [];
            $timeWhere[ $this->getSearchTimeKey() ] = $this->parseTimeWhere();
            $where                                  = array_merge( $where,$timeWhere );
        }
	
        $options = [
            'field' => $this->getTableColum() ,
            'order' => $this->getSearchOrderKey(),
            'where' => $where
        ];
        return $options;
    }
    /**
     * @description 设置固定条件
     * @return array
     */
    protected function searchTemporary(){return [];}

    /**
     * 获取无分页数据列表
     *
     * @return array
     */
    public function getNoPageList()
    {
        $options = $this->buildWhere();
        $model = $this->modelObj;
        $data  = $model->field( $options['field'] )
            ->order( $options['order'] )
            ->where( $options['where'])
            ->select();
//        $data  = $model->field( $this->hideenComment(),true )
//            ->order( $model::$id_d . $model::DESC )
//            ->select();

        if( empty( $data ) ){
            return [];
        }

        $data = ( new ArrayChildren( $data ) )->convertIdByData( $model::$id_d );

        $_SESSION[$this->getCacheKey()] = $data;

        return $data;
    }

    /**
     * 获取 单条数据
     * @return array
     */
    public function getFindOne()
    {
        $model = $this->modelObj;

        $id = $this->data[ $model::$id_d ];
        
        $data = $_SESSION[$this->getCacheKey()][ $id ];

        return $data;
    }

    /**
     * 审核（后期移植，此不属于基类）
     */
    public function approval()
    {
        $data = $this->data;

        if( empty( $data ) ){
            return false;
        }
        $model = $this->modelObj;

        $data[ $model::$status_d ] = $this->status;

        $this->modelObj->startTrans();

        $status = $this->modelObj->save( $data );

        if( !$this->modelObj->traceStation( $status ) ){
            return false;
        }

        return $status;
    }

    /**
     * @param number $status
     */
    public function setStatus( $status )
    {
        $this->status = $status;
    }

    /**
     * 析构方法
     */
    public function __destruct()
    {
        unset( $this->data,$this->modelObj );
    }

    /**
     * 对象克隆
     */
    public function __clone()
    {
        $this->data = $this->data; // 按值传值

        $this->modelObj = clone $this->modelObj;
    }

    /**
     * 获取结果
     */
    abstract public function getResult();

    /**
     * 获取当前模型类名
     */
    abstract public function getModelClassName();

    /**
     * 获取要隐藏注释的字段
     *
     * @return array
     */
    protected function hideenComment()
    {
        return [];
    }

    /**
     * 获取表字段注释【隐藏】
     * @return array
     */
    public function getComment()
    {
        $hidden = $this->hideenComment();

        return $this->modelObj->getComment( $hidden );
    }

    /**
     * 获取显示的注释
     */
    public function getShowComment()
    {
        $hidden = $this->showComment();

        return $this->modelObj->getShowComment( $hidden );
    }

    /**
     * 获取要显示注释的字段
     * @return array
     */
    protected function showComment()
    {
        return [];
    }

    /**
     * 获取表注释（表名）
     * @return string
     */
    public function tableComment()
    {
        return mb_substr( $this->modelObj->getAllTableNotes( $this->modelObj->getTableName() ),0,-1 );
    }

    /**
     * 获取本模型静态属性【数据库字段】
     * @return array
     */
    protected function getStaticProperties() :array
    {
        $colum = ( new \ReflectionObject( $this->modelObj ) )->getStaticProperties();

        $colum = ( new ArrayChildren( $colum ) )->deleteKeyByArray( '_d' );

        return $colum;
    }

    /**
     * 获取本表字段
     *
     * @return array
     */
    protected function getTableColum() :array
    {

        return array_values( $this->getStaticProperties() );
    }

    /**
     * 根据其他模型数据 获取相应的数据 适应于一对一关系
     * @param array $field 字段
     * @param string $colum 以哪个字段作为筛选条件字段
     * @return array
     */
    protected function getDataByOtherModel( array $field,$colum ) :array
    {

        $data = $this->data;

        if( empty( $field ) || empty( $colum ) ){
            return $data;
        }

        $dbFields = $this->getTableColum() ;

        if( !in_array( $colum,$dbFields ) ){
            return $data;
        }

        $idString = $this->getIdString();


        if( empty( $idString ) ){
            return $data;
        }
        // order by instr('3,2,3,12,1',concat(',',id,',')

        $model = &$this->modelObj;

        $where = $colum . ' in (%s)';

        $where = $this->parseWhere($where);

        $getData = $model->field( $field )
            ->where( $where, $idString )
            ->order( 'SUBSTRING_INDEX("' . $idString . '",' . $colum . ', 1)' )
            ->select();
        if( empty( $getData ) ){
            return $data;
        }

        foreach( $getData as $key => &$value ){

            if( !array_key_exists( $colum,$value ) ){
                continue;
            }

            $value[ $this->splitKey ] = $value[ $colum ];
            if( $this->splitKey === $colum ){
                unset( $getData[ $key ][ $where ] );
            }
        }
        
        $data = $this->parseReflectionData($getData);

        return $data;
    }
	
    /**
     * getDataByOtherModel附属方法
     */
    protected function parseReflectionData(array $getData) :array
    {
    	$model = & $this->modelObj;
    	
    	$data = Tool::oneReflectManyArray( $getData,$this->data, $model::$id_d,$this->splitKey );
    	
    	return $data;
    }
    
    /**
     * getDataByOtherModel 附属方法
     */
    protected function parseWhere($where)
    {
    	return $where;
    }
    
    /**
     * 根据字段获取字符串
     * @return string
     */
    protected function getIdString() :string
    { 
        return Tool::characterJoin( $this->data,$this->splitKey );
    }

    /**
     * 根据主表数据查从表数据
     * @return array
     */
    public function getSlaveDataByMaster() :array
    {
    	
    	$data = $this->getSlaveData();
    	
    	if (empty($data)) {
    		return $this->data;
    	}
    	
    	$slaveData = $this->parseSlaveData($data, $this->getSlaveColumnByWhere() );
    	
    	return $slaveData;
    }
	
    /**
     * 获取 从数据
     * @return array|array|unknown
     */
    protected function getSlaveData() :array
    {
    	$data = $this->data;
    	if (empty($data)) {
    		return [];
    	}
    	
    	$field = $this->getSlaveField();
    	
    	$idString = Tool::characterJoin($data, $this->splitKey);
    	
    	if (empty($idString)) {
    		return $this->data;
    	}
    	
    	$slaveColumnWhere = $this->getSlaveColumnByWhere();
    	
    	$where = $slaveColumnWhere.' in (%s)';
    	
    	//再次处理
    	$where = $this->parseSlaveWhereAgain($where);
    	
    	$slaveData = $this->modelObj->field($field)->where($where, $idString)->select();
    	
    	if (empty($slaveData)) {
    		return [];
    	}
    	
    	return $slaveData;
    }
    
    
    /**
     * 数据处理组合
     * @param array $slaveData
     * @param string $slaveColumnWhere
     * @return array
     */
    protected function parseSlaveData(array $slaveData, $slaveColumnWhere) :array
    {
        $data = $this->data;
        foreach( $slaveData as $key => &$value ){
            if( empty( $data[ $value[$slaveColumnWhere] ] ) ){
                continue;
            }
            $value = array_merge( $value, $data[ $value[$slaveColumnWhere] ]);
        }
        return $slaveData;
    }
    
    /**
     * 获取从表字段（根据主表数据查从表数据的附属方法）
     * @return array
     */
    protected function getSlaveField() :array
    {
        return [];
    }

    /**
     * 获取从表生成where条件的字段（根据主表数据查从表数据的附属方法）
     */
    protected function getSlaveColumnByWhere() :string 
    {
        $model = $this->modelObj;
        return $model::$id_d;
    }
	
    /**
     * 再次处理where 根据主表数据查从表数据
     * @return string
     */
    protected function parseSlaveWhereAgain($where) :string
    {
    	$model = $this->getModelClassName();
    	return $where.' and '.$model::$storeId_d.'='.$_SESSION['store_id'];
    }
    
    /**
     * 分页读取数据
     * @return array
     */
    protected function getDataByPage( array $options,$isNoSelect = false ) :array
    {
        if( empty( $options ) ){
            return array();
        }

        $pageNumber = $this->getPageNumber();

        $options = $this->parseOption($options);
        
        $count = !empty( $options[ 'where' ] ) ? $this->modelObj->where( $options[ 'where' ] )->count() : $this->modelObj->count();
		
        $reflection = new \ReflectionClass( C( 'page_class' ) );

        $page = $reflection->newInstanceArgs( [ $count,$pageNumber ] );

        $param = $this->data;

        Hook::listen( 'Search',$param );

        $reflection->getProperty( 'parameter' )->setValue( $page,$param );

        $options[ 'limit' ] = $page->firstRow . ',' . $page->listRows;

        $model = $this->modelObj;

        $data            = $model->field( $options[ 'field' ],$isNoSelect )->where( $options[ 'where' ] )->order( $options[ 'order' ] )->limit( $options[ 'limit' ] )->select();
        $array[ 'data' ] = $data;

        // $array['page'] = $reflection->getMethod('show')->invoke($page);

        $array[ 'page' ]    = $count;
        $array['page_size'] = $pageNumber;
        
        return $array;
    }
    
    /**
     * 获取分页数目
     */
    protected function getPageNumber() :int
    {
        return C('union_page_number');
    }
    
    /**
     * 处理条件
     * @return array
     */
    protected function parseOption(array $options) :array
    {
        $storeId = $_SESSION['store_id'];
        
        $model = $this->modelObj;
        
        $options[ 'where' ][ $model::$storeId_d ] = $storeId;
        
        return $options;
    }
    
   

    /**
     * 获取提示消息
     * @return string[][]
     */
    public function getMessageNotice() :array 
    {
        return [];
    }

    /**
     * 检测id是否是数字类型
     * @return boolean
     */
    public function checkIdIsNumric() :bool
    {
        $model = &$this->modelObj;

        if( !empty( $this->data ) && is_numeric( $this->data[ $model::$id_d ] ) ){
            return true;
        }

        $this->errorMessage = 'id必须是数字';

        return false;
    }

      
    /**
     * 添加
     */
    public function addData()
    {
        $status = false;
        
        $data = $this->getParseResultByAdd() ;
        
        try {
            $status = $this->modelObj->add($data);
        } catch (\Exception $e) {
            $this->errorMessage = '不允许重复数据（'.$e->getMessage().'）';
        }
        return $status;
    }
    
    /**
     * 保存
     */
    public function saveData() :bool
    {
        $status = false;
        
        $data = $this->getParseResultBySave();
        try {
            $status = $this->modelObj->save($data);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
        return $status !== false;
    }
    
    /**
     * 批量添加
     * @return boolean
     */
    public function addAll() :bool
    {
    	$data = $this->getParseResultByAddAll();
    	
    	try {
    		$status = $this->modelObj->addAll($data);
    	} catch (\Exception $e) {
    		$this->errorMessage = $e->getMessage().'，添加失败';
    		return false;
    	}
    	
    	return true;
    }
    
    /**
     * 批量添加时处理
     * @return array
     */
    protected function getParseResultByAddAll()
    {
    	
    	return $this->data;
    }
    
    
    /**
     * 修改状态时保存
     */
    public function chanageSaveStatus()
    {

    	$data = $this->getParseResultBySaveStatus();
    	
    	try {
    		$status = $this->modelObj->save($data);
    	} catch (\Exception $e) {
    		$this->errorMessage = $e->getMessage();
    	}
    	return $status;
    }
    
    /**
     * 保存状态时处理参数
     */
    protected function getParseResultBySaveStatus() :array
    {
    	return $this->data;
    }
    /**
     * 保存时处理参数
     * @return array
     */
    protected function getParseResultBySave() :array 
    {
        return $this->data;
    }
    
    /**
     * 添加时处理参数
     * @return array
     */
    protected function getParseResultByAdd() :array
    {
        return $this->data;
    }
    

    /**
     * 删除
     */
    public function delete()
    {
        $model = $this->modelObj;
        return $model->delete( $this->data[ $model::$id_d ] );
    }

    
    /**
     * 要更新的数据【已经解析好的】
     * @return array
     */
    protected function getDataToBeUpdated():array 
    {
        return [];
    }
    
    /**
     * 要更新的字段
     * @return array
     */
    protected function getColumToBeUpdated():array
    {
        return [];
    }
    
    /**
     * 批量更新 组装sql语句【核心】
     * @return string
     */
    protected function buildUpdateSql() :string
    {
        $parseData = $this->getDataToBeUpdated();
         
        if (!is_array($parseData)) {
            return null;
        }
         
        $keyArray = $this->getColumToBeUpdated();
         
        if (!is_array($keyArray)) {
            return null;
        }
         
        $sql = 'UPDATE '.$this->modelObj->getTableName().'  SET ';
         
        $flag = 0;
         
        $primaryKey = $this->bitchPrimaryKey();
         
        $coulumValue = null;
         
        foreach ($keyArray as $k => $v) {
            $sql .=  '`'.$v.'`' .'= CASE '. '`'.$primaryKey.'`';
            foreach ($parseData as $a => $b)
            {
                $coulumValue = is_string($b[$flag]) ? '"'.$b[$flag].'"' : $b[$flag];
                 
                $sql .= sprintf(" WHEN %s THEN %s \t\n ", $a, $coulumValue);
            }
            $flag++;
            $sql .='END,';
        }
         
        $sql = substr($sql, 0, -1);
         
        $where = ' WHERE `'.$primaryKey.'` in('.implode(',', array_keys($parseData)).')';
        //监听条件
        Event::listen('sql_update_where', $where);
        $sql .= $where;
        return $sql;
    }
    
    /**
     * 获取批量更新条件字段
     * @return string
     */
    protected function bitchPrimaryKey() :string
    {
        $modelName = $this->getModelClassName();
         
        $primaryKey = $modelName::$id_d;
        
        return $primaryKey;
    }
    
    /**
     * 获取缓存key
     * @return string
     */
    protected function getCacheKey() :string
    {
        if (empty($_SESSION['store_id'])) {
            throw new \Exception('系统异常');
        }
    
        $key = 'what_sr_'.$_SESSION['store_id'].'4564_data';
    
        return $key;
    }
    
    /**
     * 事务消息
     * @return bool
     */
    protected function traceStation($status) :bool
    {
        if ($status === false) {
            $this->modelObj->rollback();
            $this->errorMessage .= '、事务更新失败';
            return false;
        }
        return true;
    }
    
    /**
     * 获取主键编号
     */
    public function getPrimaryKey() :string
    {
    	$model = $this->getModelClassName();
    	
    	return $model::$id_d;
    }
}
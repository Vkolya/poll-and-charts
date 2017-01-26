<?php

namespace vkolya\pollandcharts;

use yii\base\Widget;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\View;

/**
 * An widget to create polls and to wrap google chart for Yii Framework 2
 * by Vantukh Mykola
 *
 * @see https://github.com/ScottHuangZL/yii2-google-chart
 * @author Vantukh Mykola <zhiliang.huang@gmail.com>
 */

class myPoll extends Widget {
   
    public $pullName;
    /**
     * @var array $answerOptions options of poll
     */
    public $answerOptions = [];
    /**
     * @var string $answerOptionsData serialized options of poll
     */
    public $answerOptionsData;
    /*
     * @var boolean $isVote shows if user is voted
     */
    public $isVote;
    /*
     * @var array $voicesData array of variants of poll and amount of voices
     */
    public $voicesData = [];
    
    public $sumOfVoices;
     /**
     * @var string $userAnswer user's answer
     */
    public $userAnswer;
   
    /**
     * @var array $diagrammsOptions additional configuration options
     * @see https://google-developers.appspot.com/chart/interactive/docs/customizing_charts
     */
    public $diagrammsOptions =[];
     /**
     * @var string $visualization the type of visualization -ie PieChart
     * @see https://google-developers.appspot.com/chart/interactive/docs/gallery
     */
    public $visualization;
     /**
     * @var array $data the data to configure visualization
     * @see https://google-developers.appspot.com/chart/interactive/docs/datatables_dataviews#arraytodatatable
     */
    public $data = [];
    /*
    * @var string $containerId the container Id to render the visualization to
    */
    public $containerId;
     /**
     * @var array $htmlOption the HTML tag attributes configuration
     */
    public $htmlOptions = [];
     /**
     * @var string $packages the type of packages, default is corechart
     * @see https://google-developers.appspot.com/chart/interactive/docs/gallery
     */
    public $packages = 'corechart';  // such as 'orgchart' and so on.
    public $loadVersion = "1"; //such as 1 or 1.1  Calendar chart use 1.1.  Add at Sep 16
    
    

    function init() {
        
        parent::init();
        $pollDb = new myPollDb();
        if($pollDb->isTablesExists() == false) {
            $pollDb->createTables();
        }
        
        if ($this->pullName == null) {
            $this->pullName = 'MyPoll';
        }

        if ($this->answerOptions != null) {
            $this->answerOptionsData = serialize($this->answerOptions);
        }

       if (!$pollDb->isPullExist($this->pullName)) {
            $pollDb->setDbData($this->pullName,$this->answerOptionsData);

            $pollDb->setVoicesData($this->pullName, $this->answerOptions);
        }
        
        if (\Yii::$app->request->isAjax) {
            if ($_POST['VoicesOfPoll']['voice'] != null) {
                
                if ($_POST['poll_name'] == $this->pullName) {
                    
                    $this->userAnswer = $_POST['VoicesOfPoll'];
                    $pollDb->updateUsers($this->pullName,$this->userAnswer['voice'], $this->answerOptions);
                    $pollDb->updateUnswers($this->pullName, $this->userAnswer['voice'], $this->answerOptions);
                  
                } 
            }
        }
        
        $this->voicesData = $pollDb->getVoicesData($this->pullName);
        
        for ($i = 0; $i < count($this->voicesData); $i++) {
            $this->sumOfVoices = $this->sumOfVoices + $this->voicesData[$i]['value'];
            //add data of answers for  diagramms
            $this->data[$i] = [$this->answerOptions[$i],  intval($this->voicesData[$i]['value'])];
            
        }  
        
     $this->isVote = $pollDb->isVote($this->pullName);
     
     $id = $this->getId();
        if (isset($this->diagrammsOptions['id']) and ! empty($this->diagrammsOptions['id']))
            $id = $this->diagrammsOptions['id'];
        // if no container is set, it will create one
        if ($this->containerId == null) {

            $this->htmlOptions['id'] = 'div-chart' . $id;
            $this->containerId = $this->htmlOptions['id'];
            echo '<div'.Html::renderTagAttributes($this->htmlOptions).'></div>';
        }
        $this->registerClientScript($id);
      
    }

    function run() {
        
        $pollDb = new myPollDb();
        $model = new VoicesOfPoll;
        return $this->render('index', [
                    'model' => $model,
                    'pollDb' => $pollDb,
                    'pullName' => $this->pullName,
                    'isVote' => $this->isVote,
                    'answers' => $this->answerOptions,
                    'voicesData' => $this->voicesData,
                    'sumOfVoices' => $this->sumOfVoices,
                    'htmlOptions' =>  $this->htmlOptions,
        ]);
    }
     /**
     * Registers required scripts
     */
    public function registerClientScript($id)
    {
        
        array_unshift($this->data,  ['Task', 'Default']);
        
        $jsData = Json::encode($this->data);
        
        $jsOptions = Json::encode($this->diagrammsOptions);
        
        $script = '
                      
		google.charts.setOnLoadCallback(drawChart' . $id . ');
		var ' . $id . '=null;
                    function drawChart' . $id . '() {
                             
			var data = google.visualization.arrayToDataTable(' . $jsData . ');
                        var options = ' . $jsOptions . ';
			    ' . $id . ' = new google.visualization.' . $this->visualization . '(document.getElementById("' . $this->containerId . '"));
                            ' . $id . '.draw(data, options);
			}';
        $view = $this->getView();
        $view->registerJsFile('https://www.gstatic.com/charts/loader.js',['position' => View::POS_HEAD]);
        $view->registerJs('google.charts.load("current", {packages:["' . $this->packages . '"]});', View::POS_HEAD, __CLASS__ . '#' . $id);
        $view->registerJs($script, View::POS_HEAD, $id);
        //$view->registerJs($scriptShowDiagramms, View::POS_HEAD, $id);

}
}

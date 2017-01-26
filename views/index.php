<?php
use yii\helpers\Html;
use vkolya\poll\AjaxSubmitButton;
?>
<style>
    .mypoll {
        width: 350px;
        height: auto;
        margin: 5px;
        padding-left: 10px;
        
    }
    .oneVote {
        width: 300px;
        height: 15px;
        background-color: #D3D3D3;
        
        position: relative;
         margin-bottom: 3px;
    
    }
    .singleLine {
        width : 150px;
        height: 15px;
        background-color: #4F9BC7;
        
       
    }
    .percent {
        position: absolute;
        display: block;
        left: 320px;
        top:5px;
    }
    .nameOfPoll {
        font-weight: bold;
        display: inline-block;
        margin-bottom: 10px;
    }
    .tick {
     float: right;
    }
    .nameOfPoll {
        display: inline-block;
        
    }

    div[id^="div-chartw"] {
   width: 350px;
   position: absolute;
   top: -9999px;
   left: -9999px;
   
}
.show_charts , .customclass {
	-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
	box-shadow:inset 0px 1px 0px 0px #ffffff;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ffffff), color-stop(1, #f6f6f6));
	background:-moz-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:-webkit-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:-o-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:-ms-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:linear-gradient(to bottom, #ffffff 5%, #f6f6f6 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#f6f6f6',GradientType=0);
	background-color:#ffffff;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #dcdcdc;
	display:inline-block;
	cursor:pointer;
	color:#2e282e;
	font-family:Arial;
	font-size:13px;
	padding:4px 16px;
	text-decoration:none;
	text-shadow:0px 1px 1px #ffffff;
        margin-top: 5px;
}
.show_charts:hover , customclass:hover{
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #f6f6f6), color-stop(1, #ffffff));
	background:-moz-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
	background:-webkit-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
	background:-o-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
	background:-ms-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
	background:linear-gradient(to bottom, #f6f6f6 5%, #ffffff 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f6f6f6', endColorstr='#ffffff',GradientType=0);
	background-color:#f6f6f6;
}
.show_charts:active, customclass:active {
	position:relative;
	top:1px;
}

#voicesofpoll-voice label { 
    padding-bottom: 5px;
        font-weight:normal; 
        color:#000000;
        letter-spacing:0pt;
        word-spacing:2pt;
        font-size:13px;
        text-align:left;
        font-family:courier new, courier, monospace;
       
}
 #voicesofpoll-voice input[type=radio] {
       margin-left: 13px;
      
 }
</style>

<div class="mypoll">
    <span class="nameOfPoll"> <?= $pullName; ?> </span><br/>
    
    <?php if (Yii::$app->user->getId() == false ) { ?>
    
    Голосовать могут только зарегистрированые пользователи!
    <?php } elseif($isVote == false && Yii::$app->user->getId() == true) { ?>
   
     <?php echo Html::beginForm('#', 'post', ['class'=>'uk-width-medium-1-1 uk-form uk-form-horizontal']); ?>
                
                <?php echo Html::activeRadioList($model,'voice',$answers); ?>
                <input type="hidden" name="poll_name" value="<?=$pullName; ?>"/>
                <?php AjaxSubmitButton::begin([
                    'label' => 'Vote',
                    'ajaxOptions' => [
                        
                        'type'=>'POST',
                        'url' => '#',
                        'success' => new \yii\web\JsExpression('function(data){
                            
                            $("body").html(data);
                            }'),
                    ],
                    'options' => ['class' => 'customclass', 'type' => 'submit'],
                    ]);
                    AjaxSubmitButton::end();
                ?>
                
               
                <?php echo Html::endForm(); 
            }
   elseif($isVote == true && Yii::$app->user->getId() == true) {              
  
   for($i = 0;$i < count($voicesData); $i++) { 
        $voicesPer = round(($voicesData[$i]['value']/$sumOfVoices),2);
            
            echo $voicesData[$i]['answers']; ?>  : <?=$voicesData[$i]['value'] ;
       
    ?>  
  <div class="oneVote">
    <?php
                $usersVote = $pollDb->getUserAnswers($voicesData[$i]['answers']);
                if ($usersVote == $voicesData[$i]['answers']) {
                    echo '<span class="tick">&#10004</span>';
                };
    ?>
    <div class="singleLine" style="width:<?php echo $voicesPer*300;?>px"></div>

    <span class="percent"><?php echo $voicesPer*100 ;?>%  </span>

</div>
   
    
   <?php  } ; ?>
       
    <input type="button" data-id="<?=$htmlOptions['id'];?>" class="show_charts" value="Show diagramm" >
       
  <?php    } ?>
       

        </div>
<script>
    $(document).ready(function(){
        
           var button = $(".show_charts");
          
           button.click(function() { 
             
                data = $(this).data("id");
                
		if (this.value == "Show diagramm") { 
                    //alert('dsfdsf');
                    $("#"+data+"").css("position","static");
                    this.value = "Hide diagramm";
		} else {
                    //alert('cccccc');
                    
                    $("#"+data+"").css("position","absolute");
                    this.value = "Show diagramm";
                    $("#"+data+"").css("position","absolute");
		  }
            });
    });
          
    </script>


<?php
  namespace frontend\widgets\myFirstWidget;
    use yii\base\Model;
    
    class VoicesOfPoll extends Model{
    public $voice;
    public $type;
    
   public function attributeLabels()
    {
        return [
            'voice' => '',
            'type' => ''
            
        ];
    }
}

       

?>
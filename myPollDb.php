<?php
 
namespace vkolya\poll;


class myPollDb  {
    
     public function isTablesExists(){
            $db = \Yii::$app->db;
            $command = $db->createCommand("SHOW TABLES LIKE 'polls'");
            $res = $command->queryAll();
            return $res;
        }
        
     public function createTables() {
         $db = \Yii::$app->db;
         $command1 = $db->createCommand("CREATE TABLE polls(id int NOT NULL AUTO_INCREMENT,"
                 . "poll_name varchar(255),answer_options text ,PRIMARY KEY (ID))")->execute();
         $command2 = $db->createCommand("CREATE TABLE users_id(id int NOT NULL AUTO_INCREMENT,"
                 . "poll_id int(11),user_id int(11), answer varchar(255),PRIMARY KEY (ID))")->execute();
         $command3 = $db->createCommand("CREATE TABLE voices_of_poll(id int NOT NULL AUTO_INCREMENT,"
                 . "poll_name varchar(255),answers varchar(255) ,value int,PRIMARY KEY (ID) )")->execute();
         
     }
     
    function setDbData($pollName , $answerOption) {
        $db = \Yii::$app->db;

        $c = $db->createCommand()->insert('polls', [
                    'poll_name' => $pollName,
                    'answer_options' => $answerOption
                ])->execute();
    } 
     
    function getUserAnswers($answer) {
       // var_dump($answer);
        $db = \Yii::$app->db;
        $user_id = \Yii::$app->user->getId();
        if($user_id == null) {
            $user_id = 0;
        }
        
           
        $command = $db->createCommand("SELECT * FROM users_id where user_id=$user_id AND answer='$answer'");
        $res = $command->queryOne();
        
        if($res == null) {
            
            return false;
            }else {
                return $res['answer'];
               // var_dump($res['answer']);
            }
        
    }
    
    function getVoicesData($pollName) {
        $db = \Yii::$app->db;
        $command = $db->createCommand("SELECT * FROM voices_of_poll where poll_name ='$pollName'");
        $voicesData = $command->queryAll();
        return $voicesData;
    }
    function setVoicesData ($pollName,$answers) {
        $db = \Yii::$app->db;
        
        for($i = 0;$i < count($answers);$i++) {
        $command = $db->createCommand()->insert('voices_of_poll',[
            'poll_name' => $pollName,
            'answers' => $answers[$i],
            'value' => 0
        ])->execute();
    }
    
    }
    function isPullExist($pollName) {
        $db = \Yii::$app->db;
        
        $query = $db->createCommand("SELECT * FROM polls where poll_name =:pollName")->
               bindParam(':pollName',$pollName);
               $data = $query->queryOne();
               if($data) {
                   return true;
               }else {
                   return false;
               }
    }
    
    
    function updateUsers($pollName,$voice,$answers) {
        $db = \Yii::$app->db;
        $query = $db->createCommand("SELECT * from polls where poll_name =:pollName")->
                 bindParam(':pollName',$pollName);
                 $pollData = $query->queryOne();
          $userId = \Yii::$app->user->getId();
          $poll_id = $pollData['id'];
          
      $answersData = $answers[$voice];   
     
      $command = $db->createCommand()->insert('users_id',[
          
                'poll_id' => $poll_id,
                'user_id' => $userId,
                'answer' => $answersData
                ])->execute(); 
                
        
    }
    function updateUnswers($pollName,$voice,$answers) {
       
        $db  = \Yii::$app->db;
        $user_id = \Yii::$app->user->getId();
        $query = $db->createCommand("UPDATE voices_of_poll SET value=value+1 "
                . "where poll_name = '$pollName' and answers = '$answers[$voice]'")->execute();
    }
    function isVote($pollName) {
        $db = \Yii::$app->db;
        $query = $db->createCommand("SELECT * from polls where poll_name =:pollName")->
                 bindParam(':pollName',$pollName);
                 $pollData = $query->queryOne();
                 
        $userId = \Yii::$app->user->getId();
        if($userId == false) { $userId = 0; };
        $poll_id = $pollData['id'];
        $command = $db->createCommand("SELECT * from users_id where poll_id=:poll_id and user_id=$userId")->
                 bindParam(':poll_id',$poll_id);
                    
        $res = $command->queryOne();
        if($res == null){
                return false;
            }else{
                return true;
            }
    }
    
    
}

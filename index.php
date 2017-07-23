<?php
/**
 * ****************************************************************************
 * xquiz - MODULE FOR XOOPS
 * Copyright (c) Mojtaba Jamali of persian xoops project (http://www.irxoops.org/)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright   	The XOOPS Project http://sourceforge.net/projects/xoops/ 
 * @license			http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         xquiz
 * @author 			Mojtaba Jamali(jamali.mojtaba@gmail.com)
 * @version      	$Id$ 
 *
 * Version : $Id:
 * ****************************************************************************
 */
require('../../mainfile.php');
$xoopsOption['template_main'] = 'quiz_index.html';
require(XOOPS_ROOT_PATH.'/header.php');
include_once XOOPS_ROOT_PATH.'/class/pagenav.php'; 
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
include_once XOOPS_ROOT_PATH.'/modules/quiz/include/functions.php';
include_once XOOPS_ROOT_PATH.'/modules/quiz/class/quiz.php';
include_once XOOPS_ROOT_PATH.'/modules/quiz/class/category.php';

try 
{
	$action = (isset($_GET['act']))? $_GET['act']:'' ;
	if (isset($_GET['q']))
	{
		if (!is_numeric($_GET['q']))
			throw new Exception(_QUIZ_NUMBER_ERROR);
		$id = $_GET['q'];
	}
	if (isset($_GET['qi']))
	{
		if (!is_numeric($_GET['qi']))
			throw new Exception(_QUIZ_NUMBER_ERROR);
		$pdid = $_GET['qi'];
	}
	$start = 0;
	if (isset($_GET['start']))
	{
		if (!is_numeric($_GET['start']))
			throw new Exception(_QUEST_SECURITY_ERROR);
		$start = $_GET['start'];
	}
	global $xoopsModuleConfig,$xoopsUser,$module_id;
	$limit = $xoopsModuleConfig['quizUserList']; // No of records to be shown per page.
	switch ($action)
	{
		case 'v':
			if (isset($id))
			{
				if (!Quiz::quiz_checkExistQuiz($id))
					throw new Exception(_QUIZ_NOT_EXIST);
				if (!Quiz::quiz_checkActiveQuiz($id))
					throw new Exception(_QUIZ_NOT_ACTIVE);
				if (!Quiz::quiz_checkExpireQuiz($id))
					throw new Exception(_QUIZ_EXPIRE);
				if (empty($xoopsUser))
				throw new Exception(_QUIZ_REGISTER_QUIZ);
				
				$perm_name = 'quiz_view';
				$cid = Quiz::quiz_quizCategory($id);
				if ($xoopsUser) {
    				$groups = $xoopsUser->getGroups();
				} else {
    				$groups = XOOPS_GROUP_ANONYMOUS;
				}
				$gperm_handler =& xoops_gethandler('groupperm'); 
				if (!$gperm_handler->checkRight($perm_name, $cid, $groups, $module_id))
					throw new Exception(_QUIZ_PERMISSION);
				
				$ts =& MyTextSanitizer::getInstance();
				$xoopsTpl->assign('showQuiz', 1);

				$qname = Quiz::quiz_quizName($id);
				$xoopsTpl->assign('quizName', $qname['name']);
				$xoopsTpl->assign('quizDescription', $ts->previewTarea($qname['description'],1,1,1,1,1));
				
				$xoopsTpl->assign('quizCategoryId', $qname['cid']);
				$xoopsTpl->assign('quizCategory', $qname['category']);
				
				$listQuestion = Question::listQuestLoader($id);
				if (empty($listQuestion))
					throw new Exception(_QUIZ_NO_QUESTION);
				$q = 0;
				$listQuest_form = new XoopsThemeForm(_QUEST_LISTQESTFORM, "listquestfrom", $_SERVER['PHP_SELF'],'post',true);
				$quizId = new XoopsFormHidden('quizId',$id);
				foreach ($listQuestion as $key ) 
				{
					$question_answers[$q] = new XoopsFormRadio($key['qnumber']."-".$ts->previewTarea($key['question'],1,1,1,1,1).""._QUEST_SCORE." = ".$key['score'] ,
				 	$key['qnumber'] ,null ,"<hr/>");
					$question_answers[$q]->addOption(1,$key['ans1']);
					$question_answers[$q]->addOption(2,$key['ans2']);
					$question_answers[$q]->addOption(3,$key['ans3']);
					$question_answers[$q]->addOption(4,$key['ans4']);
					$listQuest_form->addElement($question_answers[$q], true);
					$q++;
				}
				$quiz_token = new XoopsFormHidden("XOOPS_TOKEN_REQUEST",$GLOBALS['xoopsSecurity']->createToken());
				$submit_button = new XoopsFormButton("", "submit", _QUEST_SUBMIT, "submit");
				$listQuest_form->addElement($submit_button,true);
				$listQuest_form->addElement($quizId ,true );
				$listQuest_form->addElement($quiz_token ,true );
				$listQuest_form->assign($xoopsTpl);
			}
			break;
			
			case 's':
				$perm_name = 'quiz_view';
				$cid = Quiz::quiz_quizCategory($id);
				if ($xoopsUser) {
    				$groups = $xoopsUser->getGroups();
				} else {
    				$groups = XOOPS_GROUP_ANONYMOUS;
				}
				$gperm_handler =& xoops_gethandler('groupperm'); 
				if (!$gperm_handler->checkRight($perm_name, $cid, $groups, $module_id))
					throw new Exception(_QUIZ_PERMISSION);
					
				if (!Quiz::quiz_checkExistQuiz($id))
					throw new Exception(_QUIZ_NOT_EXIST);
				if(empty($xoopsUser) && (!$xoopsModuleConfig['seeStat']))
					throw new Exception(_QUIZ_REGISTER_STAT);
				
				if (Quiz::quiz_checkExpireQuiz($id))
					throw new Exception(_QUIZ_NOT_EXPIRE);
				
				$perm_name = 'quiz_view';
				$cid = Quiz::quiz_quizCategory($id);
				if ($xoopsUser) {
    				$groups = $xoopsUser->getGroups();
				} else {
    				$groups = XOOPS_GROUP_ANONYMOUS;
				}
				$gperm_handler =& xoops_gethandler('groupperm'); 
				if (!$gperm_handler->checkRight($perm_name, $cid, $groups, $module_id))
					throw new Exception(_QUIZ_PERMISSION);
				
				$xoopsTpl->assign('showQuiz', 2);
				$qname = Quiz::quiz_quizName($id);
				$xoopsTpl->assign('quizName', $qname['name']);
				$xoopsTpl->assign('quizDescription', $qname['description']);
				$xoopsTpl->assign('quizCategoryId', $qname['cid']);
				$xoopsTpl->assign('quizCategory', $qname['category']);
				
			$eu = ($start - 0);
			$nume = numUserScore($id);
			////////////////////////////////////////
			$listQuiz = array();
			global $xoopsModuleConfig;
			$dateformat = $xoopsModuleConfig['dateformat'];
			$q=1;
			$query = $xoopsDB->query(' SELECT * FROM ' . $xoopsDB->prefix('quiz_users').
			' WHERE id = '.$id.' ORDER BY score DESC LIMIT '.$eu.' , '.$limit);
			while($myrow = $xoopsDB->fetchArray($query) )
			{
				$listQuiz[$q]['id'] = $myrow['id'];
				$listQuiz[$q]['userid'] = $myrow['userid'];
				
				$thisUser =& $member_handler->getUser($myrow['userid']);
				
				$listQuiz[$q]['uname'] = $thisUser->getVar('uname');
				$listQuiz[$q]['name'] = $thisUser->getVar('name');
				$listQuiz[$q]['score'] = $myrow['score'];
				$listQuiz[$q]['date'] = formatTimestamp(strtotime($myrow['date']),$dateformat);
				$q++;
			}
			////////////////////////////////////////
			$xoopsTpl->assign('quizStat', $listQuiz);	
			$nav  = new XoopsPageNav($nume ,$limit ,$start ,'start' ,"act=s&q=$id");
			echo "<div align='center'>".$nav->renderImageNav().'</div><br />';
			break;
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		case 'p':
			if(empty($xoopsUser))
				throw new Exception(_QUIZ_USER_PROFILE);
			$user = $xoopsUser->getVar("uid");
			
			if (isset($pdid))
			{
				$list = userQuestLoader($pdid ,$user);
				$xoopsTpl->assign('showQuiz', 4);
				$xoopsTpl->assign('userid', $user);
				$xoopsTpl->assign('questProfile', $list);
			}
			else 
			{
				$list = userQuizzes($user);
				$xoopsTpl->assign('showQuiz', 3);
				$xoopsTpl->assign('userid', $user);
				$xoopsTpl->assign('quizProfile', $list);
				$xoopsTpl->assign('quizProfileConfig',$xoopsModuleConfig['seeScoreProfile']);
			}
			break;
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
		default:
			$cid = 0;
			if ( isset($_GET['cid']) && is_numeric($_GET['cid']) )
					$cid = $_GET['cid'];
			if ((!Category::checkExistCategory($cid)) && $cid !=0 )
					throw new Exception(_QUIZ_NOT_EXIST);
			$xt = new Category($xoopsDB->prefix('quiz_cat'), 'cid', 'pid');
			
			$parentId = -1;
			if($cid > 0)
			$parentId = $xt->categoryPid( $cid);
			$xoopsTpl->assign('Parent', $parentId);
			
			$listCategory = $xt->getPermChildArray($cid,'weight asc');			
			$xoopsTpl->assign('listCategory', $listCategory);
			$categoryNum = count($listCategory);
			$xoopsTpl->assign('categoryNum', $categoryNum);
			
			$listQuiz = Quiz::quiz_listQuizLoader($start,$limit ,$cid);
			
			$count = 0;
			foreach ($listQuiz as $key)
			{
				if ($key['status'] == 1)
					$count++;
			}
			$nav  = new XoopsPageNav($count ,$limit ,$start ,'start',"?cid=$cid" );
			echo "<div align='center'>".$nav->renderImageNav().'</div><br />';
			$xoopsTpl->assign('listQuiz', $listQuiz);
			$xoopsTpl->assign('quizNum', $count);
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if (isset($_POST['submit']))
	{      	
		if (!$GLOBALS['xoopsSecurity']->check()) 
   			throw new Exception(_QUEST_SECURITY_ERROR );
		
		if(empty($xoopsUser))
				throw new Exception(_QUIZ_REGISTER_QUIZ);
		
		$myts = myTextSanitizer::getInstance();
		$quizId = $myts->addslashes($_POST['quizId']);
		$user = $xoopsUser->getVar("uid");
		$userQuizScore = findUserScore($user,$quizId);
		if ($userQuizScore)
			throw new Exception(_QUIZ_DUBLICATE_QUIZ);
			
		
		$listQuestion = Question::listQuestLoader($quizId);
		$userScore = 0;
		$query = "INSERT INTO ". $xoopsDB->prefix('question_user') ."
			 (questId ,quizId ,userId ,userAns) VALUES ";
		$delim = '';
		foreach ($listQuestion as $key)
		{
			$query .= $delim;
			if (isset($_POST[$key['qnumber']]))
			{
				if ($myts->addslashes($_POST[$key['qnumber']]) == $key['answer'])
					$userScore += $key['score'];
			}
			$id = $key['id'];
			$ans = $myts->addslashes($_POST[$key['qnumber']]);
			$query .= "('$id','$quizId','$user','$ans')";
			$delim = ',';
	 	}
		$res = $xoopsDB->query($query);
		if(!$res)
			throw new Exception(_QUIZ_DATABASE);
		
		$date = date(DATE_ATOM); 
			$query = "INSERT INTO ". $xoopsDB->prefix('quiz_users') ."
			 (id ,userid ,score ,date) VALUES('$quizId','$user','$userScore','$date')";
		$res = $xoopsDB->query($query);
		if(!$res)
			throw new Exception(_QUIZ_DATABASE);
		if ($xoopsModuleConfig['mailScore'])
			sendEmail($user,$userScore,$quizId);
		$quizScore = '';
		if ($xoopsModuleConfig['seeScore'])
			$quizScore = "<br/>"._QUIZ_FINAL_SCORE." = ".$userScore;
		throw new Exception(_QUIZ_ADD_SCORE.$quizScore);
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
catch (Exception $e)
{
	redirect_header(XOOPS_URL.'/modules/quiz/index.php', 3, $e->getMessage());
}

include XOOPS_ROOT_PATH.'/include/comment_view.php';
require(XOOPS_ROOT_PATH.'/footer.php');


<?php
//This page is intended to be hit from a cron job only.
//Need to add some code to prevent it from being accessed any other way, but leave it exposed for now for testing.
require_once('email_functions.php');
require_once('CGICommonCode.php');
require_once('error_functions.php');
$query="SELECT emailqueueid, emailto, emailfrom, emailcc, emailsubject, body from EmailQueue ";
$query.="WHERE status=1 ORDER BY emailtimestamp LIMIT 99";
if (!$result=mysql_query($query,$link)) {
    $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
    error_log($message);
    //RenderError($title,$message);
    exit();
    }
$rows=mysql_num_rows($result);
if ($rows==0) exit();
$numGood=0;
$numBad=0;
for ($i=0; $i<$rows; $i++) {
    $row=mysql_fetch_array($result, MYSQL_BOTH);
    $headers="From: ".$row['emailfrom']."\r\nBCC: ".$row['emailcc'];
    if (mail($row['emailto'],$row['emailsubject'],$row['body'],$headers)) {
            //succeeded
            $goodList.=sprintf("%d,",$row['emailqueueid']);
            $numGood++;
            }
        else {
            //failed
            $badList.=sprintf("%d,",$row['emailqueueid']);
            $numBad++;
            }
    }
$goodList=substr($goodList,0,-1); //remove final trailing comma
$badList=substr($badList,0,-1); //remove final trailing comma
//echo "Num good: $numGood. Num bad: $numBad.<BR>\n";
//echo "Good list: $goodList <BR>\n";
//echo "Bad list: $badList <BR>\n";
if ($numGood>0) {
    $query="UPDATE EmailQueue SET status=2 WHERE emailqueueid in ($goodList)";
    if (!$result=mysql_query($query,$link)) {
        $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
        error_log($message);
        //RenderError($title,$message);
        exit();
        }
    }
if ($numBad>0) {
    $query="UPDATE EmailQueue SET status=3 WHERE emailqueueid in ($badList)";
    if (!$result=mysql_query($query,$link)) {
        $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
        error_log($message);
        //RenderError($title,$message);
        exit();
        }
    }
exit();
?>

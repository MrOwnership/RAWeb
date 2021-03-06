<?php

function RenderCommentsComponent(
    $user,
    $numComments,
    $commentData,
    $articleID,
    $articleTypeID,
    $forceAllowDeleteComments
) {
    $userID = getUserIDFromUser($user);

    echo "<div class='commentscomponent'>";

    echo "<div class='leftfloat'>";
    if ($numComments == 0) {
        echo "No comments yet. Will you be the first?<br>";
    } else {
        echo "Recent comment(s):<br>";
    }
    echo "</div>";

    if (isset($user)) {
        $subjectType = \RA\SubscriptionSubjectType::fromArticleType($articleTypeID);
        if ($subjectType !== null) {
            $isSubscribed = isUserSubscribedToArticleComments($articleTypeID, $articleID, $userID);
            echo "<div class='smalltext rightfloat'>";
            RenderUpdateSubscriptionForm("updatesubscription", $subjectType, $articleID, $isSubscribed);
            echo "<a href='#' onclick='document.getElementById(\"updatesubscription\").submit(); return false;'>";
            echo    "(" . ($isSubscribed ? "Unsubscribe" : "Subscribe") . ")";
            echo "</a>";
            echo "</div>";
        }
    }

    echo "<table id='feed'><tbody>";

    $lastID = 0;
    $lastKnownDate = 'Init';

    for ($i = 0; $i < $numComments; $i++) {
        $nextTime = $commentData[$i]['Submitted'];

        $dow = date("d/m", $nextTime);
        if ($lastKnownDate == 'Init') {
            $lastKnownDate = $dow;
        //echo "<tr><td class='date'>$dow:</td></tr>";
        } elseif ($lastKnownDate !== $dow) {
            $lastKnownDate = $dow;
            //echo "<tr><td class='date'><br>$dow:</td></tr>";
        }

        if ($lastID != $commentData[$i]['ID']) {
            $lastID = $commentData[$i]['ID'];
        }

        $canDeleteComments = ($articleTypeID == 3) && ($userID == $articleID);
        $canDeleteComments |= $forceAllowDeleteComments;

        RenderArticleComment(
            $articleID,
            $commentData[$i]['User'],
            $commentData[$i]['CommentPayload'],
            $commentData[$i]['Submitted'],
            $user,
            $articleTypeID,
            $commentData[$i]['ID'],
            $canDeleteComments
        );
    }

    if (isset($user)) {
        //    User comment input:
        $commentInputBoxID = 'art_' . $articleID;
        RenderCommentInputRow($user, $commentInputBoxID, $articleTypeID);
    }

    echo "</tbody></table>";
    echo "<br>";

    echo "</div>";
}

function RenderTopicCommentPayload($payload)
{
    echo parseTopicCommentPHPBB(nl2br($payload));
}

function RenderArticleComment(
    $articleID,
    $user,
    $comment,
    $submittedDate,
    $localUser,
    $articleTypeID,
    $commentID,
    $allowDelete
) {
    $class = '';
    $deleteIcon = '';

    if ($user == $localUser || $allowDelete) {
        $class = 'localuser';

        $img = "<img src='" . getenv('ASSET_URL') . "/Images/cross.png' width='16' height='16' alt='delete comment'/>";
        $deleteIcon = "<div style='float: right;'><a onclick=\"removeComment($articleID, $commentID); return false;\" href='#'>$img</a></div>";
    }

    $artCommentID = "artcomment_" . $articleID . "_" . $commentID;
    echo "<tr class='feed_comment $class' id='$artCommentID'>";

    //$niceDate = date( "d M\nH:i ", $submittedDate );
    $niceDate = date("j M\nG:i Y ", $submittedDate);

    echo "<td alt='Test' class='smalldate'>$niceDate</td>";
    echo "<td class='iconscommentsingle'>" . GetUserAndTooltipDiv($user, true) . "</td>";
    echo "<td class='commenttext' colspan='3'>$deleteIcon$comment</td>";

    echo "</tr>";
}

function RenderCommentInputRow($user, $rowIDStr, $artTypeID)
{
    $userImage = "<img alt='$user' title='$user' class='badgeimg' src='/UserPic/" . $user . ".png' width='32' height='32' />";
    $formStr = "<textarea id='commentTextarea' rows=0 cols=30 name='c' maxlength=250></textarea>";
    $formStr .= "&nbsp;";
    $formStr .= "<img id='submitButton' src='" . getenv('ASSET_URL') . "/Images/Submit.png' alt='Submit' style='cursor: pointer;' onclick=\"processComment( '$rowIDStr', '$artTypeID' )\" />";

    echo "<tr id='comment_$rowIDStr'><td></td><td class='iconscommentsingle'>$userImage</td><td colspan='3'>$formStr</td></tr>";
}

function RenderArticleEmptyComment($articleType, $articleID)
{
    $rowID = "art_$articleID";

    echo "<tr id='$rowID' class='feed_comment'>";

    echo "<td></td><td></td><td></td><td></td><td class='editbutton'><img src='" . getenv('ASSET_URL') . "/Images/Edit.png' width='16' height='16' style='cursor: pointer;' onclick=\"insertEditForm( '$rowID', '$articleType' )\" /></td>";

    echo "</tr>";
}

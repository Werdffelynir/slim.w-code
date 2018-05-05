<?php

namespace components;


class HString {

    public static function explodeTags($str)
    {
        if(!empty($str)){
            $tags = ' ';
            $tags_array = explode(',',$str);
            foreach ($tags_array as $tag) {
                $tags .= " <a href=\"/search/".trim($tag)."\">".trim($tag)."</a> ";
            }
            return $tags;
        }
        return null;
    }

    public static function voteDisplay($vote)
    {
        if($vote > 0) $vote = "+$vote";
        return ($vote == 0) ? '---' : (string) $vote;
    }

    public static function limitChars($str, $length = 12, $end = '...')
    {
        if(strlen($str) <= $length)
            return $str;
        else
            return mb_strimwidth($str, 0, $length, $end);
    }


    public static function limitWords($str, $length = 4, $end = '...')
    {
        $strArr = explode(' ', $str);
        $newStr = '';
        for($i = 0; $i < $length; $i++){
            if(isset($strArr[$i]))
                $newStr .= ' '.$strArr[$i];
            else
                continue;
        }
        return $newStr;
    }



}
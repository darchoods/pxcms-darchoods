<?php namespace Cysha\Modules\Darchoods\Helpers\IRC;

/* This is a PHP5 Class for parsing mIRC color codes and
 * applying the colors via css for html output
 *
 * (c) 2007 Arne Brodowski for the TopicSpy (topicspy.com)
 * this code is freely distributyble under the terms of
 * an MIT-style license.
 *
 * The code is based on two JavaScript functions written
 * and released by Chris Chabot http://www.chabotc.nl
 * Original License:
 * // Copyright (c) 2006 Chris Chabot <chabotc@xs4all.nl>
 * //
 * // this script is freely distributable under the terms of an MIT-style license.
 * // For details, see the web site: http://www.chabotc.nl/
 *
 * Usage:
 * $mcp = new MircColorParser();
 * $topic_with_color = $mcp->colorize($topic_without_color);
 *
 */

interface ColorParser
{
    public function colorize($string);
}

class VoidColorParser implements ColorParser
{
    public function __construct() {}

    public function colorize($string)
    {
        return $string;
    }
}

class MircColorParser implements ColorParser
{
    public function __construct() {}

    public function colorize($message)
    {
        $pageBack  = 'white';
        $pageFront = 'black';
        $length    = strlen($message);
        $newText   = '';
        $bold      = false;
        $color     = false;
        $reverse   = false;
        $underline = false;
        $foreColor = '';
        $backColor = '';
        for($i=0; $i < $length; $i++) {
            switch(ord($message{$i})) {
                case 2:
                    if($bold) {
                        $newText .= '</b>';
                        $bold     = false;
                    } else {
                        $newText .= '<b>';
                        $bold    = true;
                    }
                    break;
                case 3:
                    if($color)  {
                        $newText .= '</span>';
                        $color = false;
                    }
                    $foreColor = '';
                    $backColor = '';
                    if ( @is_numeric($message{$i+1}) && ($message{$i+1} >= 0) && ($message{$i+1} <= 9)) {
                        $color = true;
                        if( @is_numeric($message{++$i+1}) && ($message{$i+1} >= 0) && ($message{$i+1} <= 9)) {
                            $foreColor = $this->getColor((($message{$i} * 10) + $message{++$i}));
                        }else{
                            $foreColor = $this->getColor($message{$i});
                        }
                        if (($message{$i+1} == ',') && @is_numeric($message{++$i+1}) && ($message{$i+1} >= 0) && ($message{$i+1} <= 9)) {
                            if ( @is_numeric($message{++$i+1}) && ($message{$i+1} >= 0) && ($message{$i+1} <= 9)) {
                                $backColor = $this->getColor((($message{$i} * 10) + $message{++$i}));
                            }else{
                                $backColor = $this->getColor($message{$i});
                            }
                        }
                    }
                    if($foreColor) {
                        /* We display everything on white background, so we don't want
                           white text without an background color.
                        if($foreColor == 'white' && $backColor == '') {
                            $foreColor = 'silver';
                        }*/
                        $newText .= '<span style="color:'.$foreColor;
                        if($backColor) {
                            $newText .= ';background-color:'.$backColor;
                        }
                        $newText .= '">';
                    }
                    break;
                case 15:
                    if($bold) {
                        $newText .= '</b>';
                        $bold     = false;
                    }
                    if($color) {
                        $newText .= '</span>';
                        $color    = false;
                    }
                    if($reverse) {
                        $newText .= '</span>';
                        $reverse  = false;
                    }
                    if($underline) {
                        $newText  .= '</u>';
                        $underline = false;
                    }
                    break;
                case 22:
                    if($reverse) {
                        $newText .= '</span>';
                        $reverse  = false;
                    } else {
                        $newText .= '<span style="color:'.$pageBack.';background-color:'.$pageFront.'">';
                        $reverse  = true;
                    }
                    break;
                case 31:
                    if($underline) {
                        $newText  .= '</u>';
                        $underline = false;
                    }else{
                        $newText  .= '<u>';
                        $underline = true;
                    }
                    break;
                default:
                    $newText .= $message{$i};
                    break;
            }

        }
        if($bold)       $newText .= '</b>';
        if($color)      $newText .= '</span>';
        if($reverse)    $newText .= '</span>';
        if($underline)  $newText .= '</u>';
        return $newText;
    }

    protected function getColor($num)
    {
        switch($num) {
            case 0:  return 'white';
            case 1:  return 'black';
            case 2:  return 'navy';
            case 3:  return 'green';
            case 4:  return 'red';
            case 5:  return 'maroon';
            case 6:  return 'purple';
            case 7:  return 'olive';
            case 8:  return 'yellow';
            case 9:  return 'lime';
            case 10: return 'teal';
            case 11: return 'aqua';
            case 12: return 'blue';
            case 13: return 'fuchsia';
            case 14: return 'gray';
            case 15: return 'silver';
            default: return $this->getColor($num-16);
        }
    }
}

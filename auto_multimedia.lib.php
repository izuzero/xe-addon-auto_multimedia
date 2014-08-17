<?php

/**
 * @brief  멀티미디어 자동 삽입 애드온
 * @author 이즈야 (contact@isizu.co.kr)
 */

if (!defined('__ZBXE__') && !defined('__XE__')) exit();

function getNoInsertedMultimediaList($content, $files = array())
{
	if (!($content && count($files))) return array();
	
	$inserted = array();
	$no_inserted = array();
	$expression = '/<((img[^>]*multimedia_src)|([embed|audio|video|controls][^>]*src))=["\']?([^>"\']+)["\']?[^>]*>/is';
	$content = strip_tags($content, '<img><embed><audio><video><controls>');
	
	preg_match_all($expression, $content, $matches);
	foreach ($matches[4] as $match)
	{
		$inserted[] = substr(strrchr($match, '/'), 1);
	}
	foreach ($files as $key=>$file)
	{
		$file_name = substr(strrchr($file->uploaded_filename, '/'), 1);
		if (preg_match('/\.(jpg|jpeg|png|gif|bmp)$/is', $file_name)) continue;
		
		$uploaded = rawurlencode($file_name);
		$uploaded_2 = urlencode($file_name);
		if (!in_array($file_name, $inserted) && !in_array($uploaded, $inserted) && !in_array($uploaded_2, $inserted) && file_exists($file->uploaded_filename))
		{
			$no_inserted[] = $file;
		}
	}
	
	return $no_inserted;
}

function getMultimediaInsertedContent($content, $files = array())
{
	foreach ($files as $file)
	{
		if (preg_match('/^./', $file->uploaded_filename))
		{
			$path = substr($file->uploaded_filename, 2);
		}
		else {
			$path = $file->uploaded_filename;
		}
		
		$content = sprintf('<img src="common/img/blank.gif" editor_component="multimedia_link" multimedia_src="%s" width="400" height="320" style="display:block;width:400px;height:320px;border:2px dotted #4371B9;background:url(./modules/editor/components/multimedia_link/tpl/multimedia_link_component.gif) no-repeat center" auto_start="false" alt="" />%s', $path, $content);
	}
	
	return $content;
}
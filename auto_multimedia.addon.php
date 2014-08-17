<?php

/**
 * @brief  멀티미디어 자동 삽입 애드온
 * @author 이즈야 (contact@isizu.co.kr)
 */

if (!defined('__ZBXE__') && !defined('__XE__')) exit();

if ($called_position == 'after_module_proc')
{
	$acts = array('procBoardInsertDocument', 'procBoardInsertComment');
	
	if (in_array($this->act, $acts))
	{
		require_once(_XE_PATH_ . 'addons/auto_multimedia/auto_multimedia.lib.php');
		
		$logged_info = Context::get('logged_info');
		
		$vars = $this->variables;
		$document_srl = $vars['document_srl'];
		$comment_srl = $vars['comment_srl'];
		
		if ($this->act == 'procBoardInsertDocument' && is_numeric($document_srl) && $addon_info->insert_document != 'N')
		{
			$args = new stdClass();
			$args->document_srl = $document_srl;
			$output = executeQuery('document.getDocument', $args);
			if (!$output->toBool())
			{
				return $output;
			}
			
			$content = $output->data->content;
			if ($logged_info->is_admin != 'Y')
			{
				$content = removeHackTag($content);
			}
			
			$oFileModel = getModel('file');
			$files = $oFileModel->getFiles($document_srl);
			
			if (count($files))
			{
				$multimedia = getNoInsertedMultimediaList($content, $files);
				if (count($multimedia))
				{
					$args = new stdClass();
					$args->document_srl = $document_srl;
					$args->content = getMultimediaInsertedContent($content, $multimedia);
					
					$output = executeQuery('addons.auto_multimedia.updateDocument', $args);
					if (!$output->toBool())
					{
						return $output;
					}
				}
			}
		}
		else if ($this->act == 'procBoardInsertComment' && is_numeric($comment_srl) && $addon_info->insert_comment != 'N')
		{
			$args = new stdClass();
			$args->comment_srl = $comment_srl;
			$output = executeQuery('comment.getComment', $args);
			if (!$output->toBool())
			{
				return $output;
			}
			
			$content = $output->data->content;
			if ($logged_info->is_admin != 'Y')
			{
				$content = removeHackTag($content);
			}
			
			$oFileModel = getModel('file');
			$files = $oFileModel->getFiles($comment_srl);
			
			if (count($files))
			{
				$multimedia = getNoInsertedMultimediaList($content, $files);
				if (count($multimedia))
				{
					$args = new stdClass();
					$args->comment_srl = $comment_srl;
					$args->content = getMultimediaInsertedContent($content, $multimedia);
					
					$output = executeQuery('addons.auto_multimedia.updateComment', $args);
					if (!$output->toBool())
					{
						return $output;
					}
				}
			}
		}
	}
}
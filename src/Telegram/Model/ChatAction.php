<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ReflectionClass;

class ChatAction
{
	const Typing = 'typing';
	const RecordVideo = 'record_video';
	const RecordVoice = 'record_voice';
	const RecordVideoNote = 'record_video_note';
	const UploadPhoto = 'upload_photo';
	const UploadVideo = 'upload_video';
	const UploadVoice = 'upload_voice';
	const UploadDocument = 'upload_document';
	const UploadVideoNote = 'upload_video_note';
	const FindLocation = 'find_location';

	static function GetAllActions(): array
	{
		$oClass = new ReflectionClass(__CLASS__);
		return $oClass->getConstants();
	}
}

?>
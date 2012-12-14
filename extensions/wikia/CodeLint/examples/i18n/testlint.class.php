<?php

class testlint extends WikiaObject {
	public function output() {
		global $app;

		echo wfMsg('proper-message');
		echo wfMsg('nonexistent-message');
		echo $this->wf->Msg('proper-object-message');
		echo $app->wf->Msg('proper-app-message');
		echo F::app()->wf->Msg('proper-f-app-message');
		echo $this->wf->msg('proper-object-message-2');
		echo $app->wf->msg('proper-app-message-2');
		echo F::app()->wf->msg('proper-f-app-message-2');
		echo $this->wf->msgForContent('proper-object-message-3');
		echo $app->wf->msgForContent('proper-app-message-3');
		echo F::app()->wf->msgForContent('proper-f-app-message-3');
		echo $this->wf->msgExt('proper-object-message-4',array('parseinline'));
		echo $app->wf->msgExt('proper-app-message-4',array('parseinline'));
		echo F::app()->wf->msgExt('proper-f-app-message-4',array('parseinline'));


	}
}

<?php
class Page extends SiteTree {

	private static $db = array(
		'ContactFormDisplay' => 'Boolean',
		'ContactFormSendTo' => 'varchar(255)',
		'ContactFormSubject' => 'Text'
	);

	private static $has_one = array(
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.ContactForm', new CheckboxField('ContactFormDisplay', 'Show'));
		$fields->addFieldToTab('Root.ContactForm', new EmailField('ContactFormSendTo', 'Send To Email'));
		$fields->addFieldToTab('Root.ContactForm', new TextField('ContactFormSubject', 'Subject'));

		return $fields;
	}
}
class Page_Controller extends ContentController {

	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code>
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
	private static $allowed_actions = array (
		'ContactForm'
	);

	public function init() {
		parent::init();
		// You can include any CSS or JS required by your project here.
		// See: http://doc.silverstripe.org/framework/en/reference/requirements
	}

	public function ContactForm() {
		if ($this->ContactFormDisplay) {
	        $fields = new FieldList( 
				new TextField('Name'),
				new EmailField('Email'),
				new TextField('Subject', 'Subject', ($this->ContactFormSubject == '') ? '' : $this->ContactFormSubjectgit),
				new TextareaField('Message')
	        );

	        $actions = new FieldList( 
				new FormAction('Submit', 'Submit') 
	        );

	        return new Form($this, 'Form', $fields, $actions);
	    }
	}

	public function Submit($data, $form) {
        $email = new Email(); 

        $email->setTo('siteowner@mysite.com');
        $email->setFrom($data['Email']);
        $email->setSubject("Contact Message from {$data["Name"]}");

        $messageBody = "
            <p><strong>Name:</strong> {$data['Name']}</p>
            <p><strong>Message:</strong> {$data['Message']}</p>
        "; 
        $email->setBody($messageBody);
        $email->send();

        return array(
            'Content' => '<p>Thank you for your feedback.</p>',
            'Form' => ''
        );
    }
}

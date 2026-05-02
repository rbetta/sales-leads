<?php
declare(strict_types = 1);
namespace Carcosa\Core\Messages;

/**
 * A trait that handles the storage and retrieval of messages associated
 * with class instances that use this trait. This implements all methods
 * of the iHasMessages interface.
 */
trait tHasMessages
{

    /**
	 * The messages in this instance.
	 * @var MessageCollection
	 */
	private MessageCollection|null $messages = null;

	/**
	 * Initialize the Messages collection.
	 */
	private function initializeMessages() : self
	{
	    if (null === $this->messages) {
    	    $this->messages = \App::make(MessageCollection::class);
	    }
	    return $this;
	}

	/**
	 * Add a message.
	 * @param Message $message
	 * @param string $field An optional field name to associate this message
	 * with. For example, this could contain the name of a user interface
	 * field whose value generated the message.
	 * @return $this
	 */
	public function addMessage(Message $message, string $field = '') : self
	{
	    $this->initializeMessages();
	    $this->messages->add($message, $field);
	    return $this;
	}

	/**
	 * Add messages to this instance from either a MessageCollection instance
	 * or another object instance that implements the iHasMessages interface.
	 * @param MessageCollection|iHasMessages $messages The messages to add to
	 * this instance.
	 * @return $this
	 */
	public function addMessages(MessageCollection|iHasMessages $messages) : self
	{
	    $this->initializeMessages();
	    if ($messages instanceof MessageCollection) {
	       $this->messages->merge($messages);
	    } else {
	        $this->messages->merge($messages->getMessages());
	    }
	    return $this;
	}

	/**
	 * Get the MessageCollection that contains this instance's messages.
	 * @return MessageCollection
	 */
	public function getMessages() : MessageCollection
	{
	    $this->initializeMessages();
	    return $this->messages;
	}

}

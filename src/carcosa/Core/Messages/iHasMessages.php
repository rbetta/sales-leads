<?php
declare(strict_types = 1);
namespace Carcosa\Core\Messages;

/**
 * An interface that defines manipulation and retrieval methods for messages.
 */
interface iHasMessages
{

    /**
	 * Add a message.
	 * @param Message $message
	 * @param string $field An optional field name the message is associated
	 * with. For example, this could contain the name of a user interface
	 * field whose value generated the message.
	 * @return $this
	 */
	public function addMessage(Message $message, string $field = '') : self;

	/**
	 * Add messages.
	 * @param MessageCollection $messages
	 * @return $this
	 */
	public function addMessages(MessageCollection $messages) : self;
	
	/**
	 * Get the MessageCollection that contains this instance's messages.
	 * @return MessageCollection
	 */
	public function getMessages() : MessageCollection;
	
}

<?php
declare(strict_types = 1);
namespace Carcosa\Core\Regex;

/**
 * A class for checking for matches against a PERL-compatible
 * regular expression. This enforces Exception usage in the case
 * of regex processing failure, eliminating human error related
 * to loose comparisons of preg_match* function return values.
 */
class Regex
{

	/**
	 * The regex to validate text against.
	 * @var string
	 */
	private string $regex;

	/**
	 * How many matches the last regex comparison returned.
	 * @var int|null This will be null before the first regex comparison.
	 */
	private int|null $matchCount = null;

	/**
	 * Construct this object.
	 * @param string $regex The PERL-compatible regular expression.
	 * @throws \InvalidArgumentException If an empty string is provided.
	 */
	public function __construct(string $regex) {
		$this->setRegex($regex);
	}

	/**
	 * Set the regex.
	 * @param string $regex
	 * @return $this
	 * @throws \InvalidArgumentException If an empty string is provided.
	 */
	private function setRegex(string $regex) : self
	{
		if ('' === $regex) {
			throw new \InvalidArgumentException(
				"An empty string was provided to " . __METHOD__
			);
		} else {
			$this->regex = $regex;
		}
		return $this;

	}

	/**
	 * Get the regex.
	 * @return string
	 */
	public function getRegex() : string {
		return $this->regex;
	}

	/**
	 * Set the number of matches the previous regex comparison returned
	 * (including replacements, if the previous operation was a replacement).
	 * @param int $count
	 * @return $this
	 */
	private function setMatchCount(int $count) : self
	{
	    $this->matchCount = $count;
	    return $this;
	}

	/**
	 * Get the number of matches the previous regex comparison returned
	 * (including replacements, if the previous operation was a replacement).
	 * @return int
	 * @throws \LogicException If no regex comparison has yet been performed.
	 */
	public function getMatchCount() : int
	{
	    if (null === ($count = $this->matchCount)) {
	        throw new \LogicException(
	            "Attempted to invoke " . __METHOD__ . " prior to " .
	            "performing any regex matching operation."
	        );
	    }
	    return $count;
	}

	/**
	 * Get the first regex match (including its captured subpatterns).
	 * @param string $text The string to match against.
	 * @param bool $includeOffsets If true, then the returned array will
	 * behave as if PHP's PREG_OFFSET_CAPTURE flag was supplied.
	 * @param int $offset The offset into the subject string to use.
	 * @return string[] The returned array will conform to the format of the
	 * preg_match() function.
	 * @throws \RuntimeException If regex processing fails (e.g. due to
	 * an invalid regex, excessive backtracking during matching, etc.).
	 */
	public function getFirstMatch(
	    string $text,
	    bool $includeOffsets = false,
	    int $offset = 0
	) : array
	{

	    $matches	= [];
	    $regex		= $this->getRegex();

	    // Set the desired regex matching flags.
	    $flags = 0 | ($includeOffsets ? PREG_OFFSET_CAPTURE : 0);

	    // Check the supplied text against the regex.
	    //
	    // Note:
	    //     The error silencing operator is used here to prevent a
	    //     spurious warning if the regex is invalid. We identify
	    //     this condition and explicitly throw an exception instead.
	    //
	    $results = @ preg_match($regex, $text, $matches, $flags, $offset);
	    if ( false === $results ) {

	        // An error occurred while processing the regex.
	        $this->handleError(__METHOD__);

	    } else {

	        // The regex comparison succeeded (regardless of whether
	        // the supplied text matched the regex).
	        $this->setMatchCount($results);
	        return $matches;

	    }

	}

	/**
	 * Get all regex matches (including their captured subpatterns).
	 * @param string $text The string to match against.
	 * @param bool $useSetOrder If true, then the match will behave as if
	 * PHP's PREG_SET_ORDER flag had been supplied to preg_match_all().
	 * @param bool $includeOffsets If tre, then the returned array will
	 * behave as if PHP's PREG_OFFSET_CAPTURE flag was supplied.
	 * @param int $offset The offset into the subject string to use.
	 * @return string[] The returned array will conform to the format
	 * of the preg_match_all() function.
	 * @throws \RuntimeException If regex processing fails (e.g. due to
	 * an invalid regex, excessive backtracking during matching, etc.).
	 */
	public function getAllMatches(
	    string $text,
	    bool $useSetOrder = false,
	    bool $includeOffsets = false,
	    int $offset = 0
	    ) : array
	    {

	        $matches	= [];
	        $regex		= $this->getRegex();

	        // Initialize the flags.
	        $flags = 0 |
	           ($useSetOrder       ? PREG_SET_ORDER        : 0) |
	           ($includeOffsets    ? PREG_OFFSET_CAPTURE   : 0);

	        // Check the supplied text against the regex.
    	    //
    	    // Note:
    	    //     The error silencing operator is used here to prevent a
    	    //     spurious warning if the regex is invalid. We identify
    	    //     this condition and explicitly throw an exception instead.
    	    //
	        $results = @ preg_match_all($regex, $text, $matches, $flags, $offset);
	        if ( false === $results ) {

	            // An error occurred while processing the regex.
	            $this->handleError(__METHOD__);

	        } else {

	            // The regex comparison succeeded (regardless of whether
	            // the supplied text matched the regex).
	            $this->setMatchCount($results);
	            return $matches;

	        }

	}

	/**
	 * Get whether text matches this regex. Only the first match is
	 * examined, so invoking Regex::getMatchCount() after this will
	 * return either 0 or 1 (never a higher number).
	 * @param string $text The text to match against.
	 * @param int $offset The desired offset into the supplied text.
	 * @return bool True if the regex matches; false otherwise.
	 * @throws \RuntimeException If the regular expression
	 * comparison fails (e.g. due to an invalid regex, excessive
	 * backtracking during matching, etc.).
	 */
	public function getIsMatch(string $text, $offset = 0) : bool
	{
	    return (bool) $this->getFirstMatch($text, false, $offset);
	}

	/**
	 * Replace text matching this regex with other specified text.
	 * @param string $text The text to perform replacement on.
	 * @param string $replacement The text that will replace any matching
	 * instances of this pattern. This can include PERL-compatible
	 * backreferences.
	 * @param int|null $limit If not null, then this many replacements will
	 * occur at maximum.
	 * @return string The text after any matching replacements are performed.
	 * @throws \InvalidArgumentException If the supplied limit is negative.
	 * @throws \RuntimeException If an error occurs during regex execution.
	 * @todo Allow the $text and $replacement arguments to be arrays.
	 */
	public function replace(string $text, string $replacement, ?int $limit = null) : string
	{
	    // Validate the limit parameter.
	    if (null !== $limit && $limit < 0) {
	        throw new \InvalidArgumentException(
	            "The invalid negative limit parameter value $limit was " .
	            "supplied to " . __METHOD__
	        );

	    }

	    // Invoke the underlying regex function.
	    $count = 0;
	    $limit = $limit ?? -1;     // -1 indicates no limit.
	    $result = preg_replace($this->regex, $replacement, $text, $limit, $count);
	    if (null === $result) {
	        $this->handleError(__METHOD__);
	    }
	    $this->setMatchCount($count);
	    return $result;
	}

	/**
	 * Handle a regex matching error.
	 * @param string $method The method where the error occurred.
	 * @return void
	 * @throws \RuntimeException Unconditionally throws this
	 * exception, setting an appropriate error message.
	 */
	private function handleError(string $method) : void
	{
	    $errorNumber   = preg_last_error();
	    $errorName     = preg_last_error_msg();
	    $regex         = $this->getRegex();

	    throw new \RuntimeException(
	        "Internal error #$errorNumber ($errorName) occurred  while " .
	        "attempting to process the regex \"$regex\" in $method."
	    );
	}

	/**
	 * Get a value escaped for use in a PERL-compatible regex.
	 * @param string $value
	 * @param string $delimiter The delimiter used in the regex. By
	 * default this will be "/" (since this is the most common
	 * delimiter used by PERL-compatible regular expressions).
	 * @return string
	 */
	public static function escape(string $value, string $delimiter = '/') : string
	{
		return preg_quote($value, $delimiter);
	}

}

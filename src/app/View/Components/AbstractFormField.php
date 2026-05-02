<?php
declare(strict_types = 1);
namespace App\View\Components;

use Carcosa\Core\Regex\RegexFactory;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * An abstract base class for any component that renders a form field.
 * @author Randall Betta
 *
 */
abstract class AbstractFormField extends Component
{
    
    /**
     * Strip the final empty index "[]" from a form field name.
     * @param string $name A form field name that ends in "[]"
     * (the empty array index identifier, which will automatically
     * generate the next array index in sequence).
     * @return string The form field with the final "[]" stripped.
     * @throw \RuntimeException If the supplied form field name
     * is either less than three characters or does not have the
     * substring "[]" at its end.
     */
    public function stripEmptyArraySyntax(string $name) : string
    {
        
        // Validate the form field name.
        if (strlen($name) < 3 || ! str_ends_with($name, "[]")) {
            throw new \RuntimeException(
                "An invalid form field name was supplied to " .
                __METHOD__ . " (expected: at least three " .
                "characters ending with an empty array index \"[]\")."
            );
        }
        
        return substr($name, 0, strlen($name) - 2);
    }
    
    /**
     * Convert a form field name that ends in an empty array index
     * (like "[]") to use a specific index instead (typically from
     * Laravel's built-in Blade $loop->index functionality).
     * @param string $name A form field name that ends in an empty
     * array index (like "[]").
     * @param string|int $index The index to use (typically using
     * Laravel's built-in Blade $loop->index functionality).
     * @return string The modified form name.
     * @throw \RuntimeException If the supplied form field name
     * does not end in "[]" with at least one preceding character.
     */
    public function toIndexedArraySyntax(string $name, string|int $index) : string
    {
        
        // Validate the form field name.
        if (strlen($name) < 3 || ! str_ends_with($name, "[]")) {
            throw new \RuntimeException(
                "An invalid form field name was supplied to " .
                __METHOD__ . " (expected: at least three " .
                "characters ending with an empty array index \"[]\")."
            );
        }
        
        // Transform the form field name to use the supplied index.
        return substr($name, 0, strlen($name) - 1) . $index . "]";
        
    }
    
    /**
     * Convert a form field name that uses square bracket syntax
     * for arrays into its corresponding Laravel dot notation syntax
     * (for example, "field[name][first]" will become "field.name.first").
     * @param string $name A field name.
     * @return string The field name with square bracket array syntax
     * converted to Laravel dot notation syntax.
     * @param bool $convertEmptyIndicesToAsterisks If true, then the
     * substring "[]" will be converted into ".*" instead of generating
     * an error.
     * @throws \RuntimeException If a malformed field name is supplied.
     * @throws \RuntimeException If any array index is an empty string
     * (since the next key for an automatically incrementing array
     * index cannot be determined without additional context), unless
     * the $convertEmptyIndicesToAsterisks argument is true. 
     */
    public function toArrayDotSyntax(
        string $name,
        bool $convertEmptyIndicesToAsterisks = false
    ) : string
    {
        
        // Note: a regex cannot easily be used to identify all
        // array indices in a capturing group, because only the
        // last captured group is returned when a repetition
        // signifier (like "*", "+", or "{1,}") is used. We
        // therefore use a simple iterating solution here
        // to determine the array name and all its indices
        // (though note that we do use a regex to validate that
        // the overall structure is not malformed).
        
        // Validate the supplied name's overall structure, using the
        // following rules:
        //
        //  1:  It must start with any number of characters that are
        //      anything except opening square brackets, closing square
        //      brackets or whitespace.
        //
        //  2:  It can optionally be followed by one or more substrings
        //      that obey all of the following rules:
        //
        //          A:  Must begin with an opening square bracket.
        //
        //          B:  Must be followed by at least one character that
        //              is neither an opening nor closing square bracket
        //              if the $convertEmptyIndicesToAsterisks argument
        //              is false.
        //
        //          C:  Must end with a closing square bracket.
        //      
        $regexFactory = \App::make(RegexFactory::class);
        $regex = $regexFactory->create('/^[^\[\]\s]+(\[([^\[\]])*\])*$/');
        if (! $regex->getIsMatch($name)) {
            throw new \RuntimeException(
                "The invalid field name \"$name\" was supplied to " .
                __METHOD__
            );
        }
        
        $results    = [];
        $i          = 0;    // The iteration through this loop.
        $parts      = explode("[", $name);
        foreach ($parts as $part) {

            if (0 === $i) {
                
                // Handle the array name.
                $results[] = $part;
                
            } else {
                
                // Handle an array index.
                $part = rtrim($part, "]");
                
                if ("" === $part) {
                    
                    // This is an integer index with no key specified,
                    // indicating that the next incrementing integer
                    // key should be automatically assigned. Since this
                    // function cannot know how many other previous times
                    // this index has been assigned automatically in the
                    // DOM, we cannot safely process this field name unless
                    // the option to automatically convert these empty
                    // indices to asterisks is enabled.
                    if ($convertEmptyIndicesToAsterisks) {
                        $part = "*";
                    } else {
                        throw new \RuntimeException(
                            "An empty array key was supplied to " .
                            __METHOD__ . ", but this method does not support " .
                            "automatically incrementing array indices."
                        );
                    }
                    
                }
                $results[] = $part;
            }
            
            // Prepare to examine the next part of the supplied name.
            $i++;
            
        }
        
        // Return the modified field name.
        return implode(".", $results);
        
    }
    
}

<?php
declare(strict_types = 1);

namespace Carcosa\Core\Util;

use Carcosa\Core\I18n\EncodingFactory;
use Carcosa\Core\I18n\TextFactory;

/**
 * A class that handles certain HTML-related operations.
 * @author Randall Betta
 *
 */
class Html
{
    
    /**
     * Get whether a value is a valid HTML "id" attribute.
     * @param string $id
     * @param string $encoding The character encoding (defaults to "utf-8").
     * @return bool
     * @todo Change the whitespace analyzer to use the
     * IntlChar::isUWhiteSpace() method instead, for
     * HTML 5 standards-compliance.
     * @todo Add the IntlChar extension to the composer.json file.
     */
    public function getIsValidId(string $id, string $encoding = 'utf-8') : bool
    {

        $encodingFactory    = \App::make(EncodingFactory::class);
        $textFactory        = \App::make(TextFactory::class);

        // Convert the HTML ID into a Text instance, so we can
        // properly handle its character encoding.
        $encoding   = $encodingFactory->createByName($encoding);
        $idText     = $textFactory->create($id, $encoding);
        
        // Obtain Text instances representing whitespace characters
        // in the given encoding.
        $whitespaceChars = [
            $textFactory->createAscii(" ")->toEncoding($encoding),
            $textFactory->createAscii("\t")->toEncoding($encoding),
            $textFactory->createAscii("\r")->toEncoding($encoding),
            $textFactory->createAscii("\n")->toEncoding($encoding),
            $textFactory->createAscii("\v")->toEncoding($encoding),
        ];
        
        // HTML IDs must have at least one character.
        if ( 0 === $idText->getCharCount() ) {
            return false;
        }
        
        // HTML IDs must not contain any whitespace.
        foreach ($whitespaceChars as $whitespaceChar) {
            if (false !== $idText->find($whitespaceChar)) {
                return false;
            }
        }
        
        // All tests succeeded. The HTML ID is valid.
        return true;
        
    }
    
}

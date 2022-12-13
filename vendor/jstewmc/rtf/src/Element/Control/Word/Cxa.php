<?php

namespace Jstewmc\Rtf\Element\Control\Word;

/**
 * The "\cxa" control word indicates text automatically generated by the CAT system,
 * like the "Q." in Questions. Keep in mind, this does not include the automatically
 * translated punctuation at the end of sentences, only text which cannot be edited
 * by the user.
 *
 * For example:
 *
 *     \par\s1{\cxa Q. }{\*\cxs STAEUT}State {\*\cxs UR}your...
 */
class Cxa extends Word
{
    public function __construct()
    {
        parent::__construct('cxa');
    }
}

<?php

namespace LucLeroy\Regex;

class RegexTest extends \PHPUnit_Framework_TestCase
{

    public function testLiteral()
    {
        $this->assertEquals('Only ordinary characters',
            Regex::create()->literal('Only ordinary characters'));
        $this->assertEquals('\$\(\)\*\+\.\?\[\^\{\|\\\\',
            Regex::create()->literal('$()*+.?[^{|\\'));
        $this->assertEquals('a*', Regex::create()->literal('a')->anyTimes());
        $this->assertEquals('(?:ab)*',
            Regex::create()->literal('ab')->anyTimes());
    }

    public function test_non_printable_chars()
    {
        $this->assertEquals('\a\e\f\n\r\t\v[\b]',
            Regex::create()->bell()->esc()->ff()->lf()->cr()->tab()->vtab()->backSpace());
        $this->assertEquals('\r\n', Regex::create()->crlf());
    }

    public function testControl()
    {
        $this->assertEquals('\cG', Regex::create()->control('g'));
    }

    public function testAnsi()
    {
        $this->assertEquals('\x1B', Regex::create()->ansi(0x1b));
    }

    public function testUnicodeChar()
    {
        $this->assertEquals('\x{123F}', Regex::create()->unicodeChar(0x123F));
    }

    public function testChars()
    {
        $this->assertEquals('[abc]', Regex::create()->chars('abc'));
        $this->assertEquals('[a-z]', Regex::create()->chars('a..z'));
        $this->assertEquals('[a-z0-9]', Regex::create()->chars('a..z0..9'));
        $this->assertEquals('[a\.z\[]', Regex::create()->chars('a.z['));
        $this->assertEquals('[\-]', Regex::create()->chars('-')); 
    }

    public function testNotChars()
    {
        $this->assertEquals('[^abc]', Regex::create()->notChars('abc'));
    }

    public function testAnyChar()
    {
        $this->assertEquals('(?s:.)', Regex::create()->anyChar());
    }

    public function testNotNewline()
    {
        $this->assertEquals('.', Regex::create()->notNewline());
    }

    public function testDigit()
    {
        $this->assertEquals('\d', Regex::create()->digit());
        $this->assertEquals('[01]', Regex::create()->digit(2));
        $this->assertEquals('[0-7]', Regex::create()->digit(8));
        $this->assertEquals('[0-9a-fA-F]', Regex::create()->digit(16));
    }

    public function testNotDigit()
    {
        $this->assertEquals('\D', Regex::create()->notDigit());
        $this->assertEquals('[^01]', Regex::create()->notDigit(2));
        $this->assertEquals('[^0-7]', Regex::create()->notDigit(8));
        $this->assertEquals('[^0-9a-fA-F]', Regex::create()->notDigit(16));
    }

    public function testWordChar()
    {
        $this->assertEquals('\w', Regex::create()->wordChar());
    }

    public function testNotWordChar()
    {
        $this->assertEquals('\W', Regex::create()->notWordChar());
    }

    public function testWhitespace()
    {
        $this->assertEquals('\s', Regex::create()->whitespace());
    }

    public function testNotWhitespace()
    {
        $this->assertEquals('\S', Regex::create()->notWhitespace());
    }

    public function testExtendedUnicode()
    {
        $this->assertEquals('\X', Regex::create()->extendedUnicode());
    }

    public function testUnicode()
    {
        $this->assertEquals('\pL', Regex::create()->unicode(Unicode::Letter));
        $this->assertEquals('\p{Ll}',
            Regex::create()->unicode(Unicode::LetterLower));
        $this->assertEquals('\p{Arabic}',
            Regex::create()->unicode(Unicode::ScriptArabic));
    }

    public function testNotUnicode()
    {
        $this->assertEquals('\PL', Regex::create()->notUnicode(Unicode::Letter));
        $this->assertEquals('\P{Ll}',
            Regex::create()->notUnicode(Unicode::LetterLower));
        $this->assertEquals('\P{Arabic}',
            Regex::create()->notUnicode(Unicode::ScriptArabic));
    }

    public function testStartOfString()
    {
        $this->assertEquals('\A', Regex::create()->startOfString());
    }

    public function testEndOfString()
    {
        $this->assertEquals('\z', Regex::create()->endOfString());
    }

    public function testEndOfStringIgnoreFinalBreak()
    {
        $this->assertEquals('\Z', Regex::create()->endOfStringIgnoreFinalBreak());
    }

    public function testStartOfLine()
    {
        $this->assertEquals('^', Regex::create()->startOfLine());
    }

    public function testEndOfLine()
    {
        $this->assertEquals('$', Regex::create()->endOfLine());
    }
    
    public function testWordLimit()
    {
        $this->assertEquals('\b', Regex::create()->wordLimit());
    }
    
    public function testNotWordLimit()
    {
        $this->assertEquals('\B', Regex::create()->notWordLimit());
    }

    public function testAlt()
    {
        $this->assertEquals('a|b',
            Regex::create()->alt([Regex::create()->literal('a'), Regex::create()->literal('b')]));
        $this->assertEquals('a|b|c',
            Regex::create()->alt([Regex::create()->literal('a'), Regex::create()->literal('b'), Regex::create()->literal('c')]));
        $this->assertEquals('a|bc',
            Regex::create()->alt([Regex::create()->literal('a'), Regex::create()->literal('bc')]));
        $this->assertEquals('(?:a|b)c',
            Regex::create()->alt([Regex::create()->literal('a'), Regex::create()->literal('b')])->literal('c'));
        $this->assertEquals('a(?:b|c)',
            Regex::create()->literal('a')->alt([Regex::create()->literal('b'), Regex::create()->literal('c')]));
        $this->assertEquals('a|b|c',
            Regex::create()->literal('a')->literal('b')->literal('c')->alt());
        $this->assertEquals('a(?:b|c)',
            Regex::create()->literal('a')->literal('b')->literal('c')->alt(2));
    }

    public function testCapture()
    {
        $this->assertEquals('a(b)c',
            Regex::create()->literal('a')->literal('b')->capture()->literal('c'));
        $this->assertEquals('a(b|c)d',
            Regex::create()->literal('a')->literal('b')->literal('c')->alt(2)->capture()->literal('d'));
        $this->assertEquals('a(?P<name>b|c)d',
            Regex::create()->literal('a')->literal('b')->literal('c')->alt(2)->capture('name')->literal('d'));
    }
    
    public function testRef()
    {
        $this->assertEquals('a(b|c)d\g{1}',
            Regex::create()->literal('a')->literal('b')->literal('c')->alt(2)->capture()->literal('d')->ref(1));
        $this->assertEquals('a(?P<name>b|c)d(?P=name)',
            Regex::create()->literal('a')->literal('b')->literal('c')->alt(2)->capture('name')->literal('d')->ref('name'));
    }
    
    public function testGroup()
    {
        $this->assertEquals('(?:abc)*',
            Regex::create()->literal('a')->literal('b')->literal('c')->group()->anyTimes());
        $this->assertEquals('a(?:bc)*',
            Regex::create()->literal('a')->start()->literal('b')->literal('c')->group()->anyTimes());
        $this->assertEquals('a(?:bc)*',
            Regex::create()->literal('a')->literal('b')->literal('c')->group(2)->anyTimes());
        $this->assertEquals('a(?:bc)*',
            Regex::create()->literal('a')->group(Regex::create()->literal('b')->literal('c'))->anyTimes());
        $this->assertEquals('a(?:b|c)*',
            Regex::create()->literal('a')->group(Regex::create()->literal('b')->literal('c')->alt())->anyTimes());
        $this->assertEquals('a(b|c)*',
            Regex::create()->literal('a')->group(Regex::create()->literal('b')->literal('c')->alt())->capture()->anyTimes());
    }
    
    public function testOptional()
    {
        $this->assertEquals('a?', Regex::create()->optional(Regex::create()->literal('a')));
        $this->assertEquals('a?', Regex::create()->literal('a')->optional());
        $this->assertEquals('(?:a|b)?', Regex::create()->literal('a')->literal('b')->alt(2)->optional());
    }
    
    public function testAnyTimes()
    {
        $this->assertEquals('a*', Regex::create()->anyTimes(Regex::create()->literal('a')));
        $this->assertEquals('a*', Regex::create()->literal('a')->anyTimes());
        $this->assertEquals('(?:a|b)*', Regex::create()->literal('a')->literal('b')->alt(2)->anyTimes());
    }
    
    public function testAtLeastOne()
    {
        $this->assertEquals('a+', Regex::create()->atLeastOne(Regex::create()->literal('a')));
        $this->assertEquals('a+', Regex::create()->literal('a')->atLeastOne());
        $this->assertEquals('(?:a|b)+', Regex::create()->literal('a')->literal('b')->alt(2)->atLeastOne());
    }
    
    public function testAtLeast()
    {
        $this->assertEquals('a{3,}', Regex::create()->atLeast(3, Regex::create()->literal('a')));
        $this->assertEquals('a{3,}', Regex::create()->literal('a')->atLeast(3));
        $this->assertEquals('(?:a|b){3,}', Regex::create()->literal('a')->literal('b')->alt(2)->atLeast(3));
        $this->assertEquals('a+', Regex::create()->literal('a')->atLeast(1));
        $this->assertEquals('a*', Regex::create()->literal('a')->atLeast(0));
    }
    
    public function testBetween()
    {
        $this->assertEquals('a{2,5}', Regex::create()->between(2, 5, Regex::create()->literal('a')));
        $this->assertEquals('a{2,5}', Regex::create()->literal('a')->between(2, 5));
        $this->assertEquals('(?:a|b){2,5}', Regex::create()->literal('a')->literal('b')->alt(2)->between(2,5 ));
        $this->assertEquals('a?', Regex::create()->literal('a')->between(0,1));
        $this->assertEquals('a{3}', Regex::create()->literal('a')->between(3,3));
    }
    
    public function testTimes()
    {
        $this->assertEquals('a{3}', Regex::create()->times(3, Regex::create()->literal('a')));
        $this->assertEquals('a{3}', Regex::create()->literal('a')->times(3));
        $this->assertEquals('(?:a|b){3}', Regex::create()->literal('a')->literal('b')->alt(2)->times(3));
    }
    
   public function testLazy()
    {
        $this->assertEquals('a*?', Regex::create()->literal('a')->anyTimes()->lazy());
        $this->assertEquals('ab*?c', Regex::create()->literal('a')->literal('b')->anyTimes()->literal('c')->group(3)->lazy());
        $this->assertEquals('ab*c', Regex::create()->literal('a')->literal('b')->anyTimes()->greedy()->literal('c')->group(3)->lazy());
        $this->assertEquals('a??b*c', Regex::create()->literal('a')->optional()->literal('b')->anyTimes()->greedy()->literal('c')->group(3)->lazy());
        $this->assertEquals('a?b*?c', Regex::create()->greedy()->literal('a')->optional()->literal('b')->anyTimes()->lazy()->literal('c')->group(3)->lazy());
    }
    
    public function testPossessive()
    {
        $this->assertEquals('a*+', Regex::create()->literal('a')->anyTimes()->possessive());
    }
    
    public function testAtomic()
    {
        $this->assertEquals('(?>a|b)', Regex::create()->literal('a')->literal('b')->alt(2)->atomic());
    }
    
    public function testNothing()
    {
        $this->assertEquals('', Regex::create()->nothing());
    }
    
    public function testBefore()
    {
        $this->assertEquals('(?<=a)', Regex::create()->literal('a')->before());
    }
    
    public function testNotBefore()
    {
        $this->assertEquals('(?<!a)', Regex::create()->literal('a')->notBefore());
    }
    
    public function testAfter()
    {
        $this->assertEquals('(?=a)', Regex::create()->literal('a')->after());
    }
    
    public function testNotAfter()
    {
        $this->assertEquals('(?!a)', Regex::create()->literal('a')->notAfter());
    }
    
    public function testMatch()
    {
        $this->assertEquals('(?(1)|(?!))', Regex::create()->match(1));
    }
    
    public function testCond()
    {
        $this->assertEquals('(?(1)a|b)', Regex::create()->match(1)->literal('a')->literal('b')->cond());
        $this->assertEquals('(?(1)a|)', Regex::create()->match(1)->literal('a')->cond());
        $this->assertEquals('(?(?<=a)b|c)', Regex::create()->literal('a')->before()->literal('b')->literal('c')->cond());
    }
    
    public function testNotCond()
    {
        $this->assertEquals('(?(1)b|a)', Regex::create()->match(1)->literal('a')->literal('b')->notCond());
        $this->assertEquals('(?(1)|a)', Regex::create()->match(1)->literal('a')->notCond());
        $this->assertEquals('(?(?<=a)c|b)', Regex::create()->literal('a')->before()->literal('b')->literal('c')->notCond());
    }
    
    public function testCaseInsensitive()
    {
        $this->assertEquals('a(?i)b(?-i)c', Regex::create()->literal('a')->literal('b')->caseInsensitive()->literal('c'));
        $this->assertEquals('a(?i)b(?-i)c', Regex::create()->literal('a')->literal('b')->caseInsensitive(true)->literal('c'));
        $this->assertEquals('a(?-i)b(?i)c', Regex::create()->literal('a')->literal('b')->caseInsensitive(false)->literal('c'));
        $this->assertEquals('(?i)abc(?-i)', Regex::create()->caseInsensitive()->literal('a')->literal('b')->literal('c'));
    }
    
    public function testCaseSensitive()
    {
        $this->assertEquals('a(?-i)b(?i)c', Regex::create()->literal('a')->literal('b')->caseSensitive()->literal('c'));
        $this->assertEquals('a(?-i)b(?i)c', Regex::create()->literal('a')->literal('b')->caseSensitive(true)->literal('c'));
        $this->assertEquals('a(?i)b(?-i)c', Regex::create()->literal('a')->literal('b')->caseSensitive(false)->literal('c'));
        $this->assertEquals('(?-i)abc(?i)', Regex::create()->caseSensitive()->literal('a')->literal('b')->literal('c'));
    }

    public function testGetRegex()
    {
        $this->assertEquals('/a/m', Regex::create()->literal('a')->getRegex());
        $this->assertEquals('%a%m', Regex::create()->literal('a')->getRegex('%'));
        $this->assertEquals('/a\//m', Regex::create()->literal('a')->literal('/')->getRegex());
        $this->assertEquals('/a/muS', Regex::create()->literal('a')->getUtf8OptimizedRegex());
    }
    
}

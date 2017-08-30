<?php

namespace luyatests\core\tag\tags;

use luya\tag\tags\MailTag;
use luyatests\LuyaWebTestCase;

class MailTagTest extends LuyaWebTestCase
{
    public function testMailObject()
    {
        $tag = new MailTag();

        $this->assertNotNull($tag->readme());
        $this->assertNotNull($tag->readme());

        $this->assertSame('<a href="mailto:hello@luya.io">hello@luya.io</a>', $tag->parse('hello@luya.io', null));
        $this->assertSame('<a href="mailto:hello@luya.io">E-Mail</a>', $tag->parse('hello@luya.io', 'E-Mail'));
    }
}

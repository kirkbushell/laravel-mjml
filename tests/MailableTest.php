<?php

namespace Tests;

use EscuelaDeMusica\MJML\Mail\Mailable;
use EscuelaDeMusica\MJML\Support\InteractsWithMjml;
use Illuminate\Mail\Mailable as IlluminateMailable;
use Illuminate\Support\Facades\Mail;

/*
  * Check tests/resouces/test.blade.php to see test file.
  *
  * <mjml>
  * <mj-body>
  * <mj-text>{{ $name }}</mj-text>
  * </mj-body>
  * </mjml>
  */

class MailableTest extends TestCase
{
    /** @test */
    public function can_render_a_mjml_template()
    {
        $mailable = new TestMailable();

        $this->assertStringContainsString(
            '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">',
            $mailable->build()->render()
        );
    }

    /** @test */
    public function can_render_template_with_data()
    {
        $mailable = new TestMailableWithData();

        $this->assertStringContainsString('John', $mailable->build()->render());
    }

    /** @test */
    public function can_use_trait_in_mailable()
    {
        $mailable = new MailableExtendsLaravelMailable();

        $this->assertStringContainsString('<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">', $mailable->build()->render());
    }

    /** @test */
    public function can_use_mailable_in_mail_helper()
    {
        $mailable = new TestMailableWithData();

        Mail::to('staff@escuelademusica.com')->send($mailable);

        $mailable->assertSeeInHtml('John');
        $mailable->assertSeeInHtml('<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">');
    }
}

class TestMailable extends Mailable
{
    public function build()
    {
        return $this->mjml('test');
    }
}

class TestMailableWithData extends Mailable
{
    public function build()
    {
        return $this->mjml('test', ['name' => 'John']);
    }
}

class MailableExtendsLaravelMailable extends IlluminateMailable
{
    use InteractsWithMjml;

    public function build()
    {
        return $this->mjml('test', ['name' => 'John']);
    }

    public function render()
    {
        return $this->renderMjml();
    }
}

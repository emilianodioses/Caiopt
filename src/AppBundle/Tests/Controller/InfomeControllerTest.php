<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InfomeControllerTest extends WebTestCase
{
    public function testAfipventasalicuotas()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/afipventasalicuotas');
    }

}

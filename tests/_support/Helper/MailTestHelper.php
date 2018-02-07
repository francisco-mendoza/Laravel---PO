<?php


namespace Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

use GuzzleHttp;

class MailTestHelper
{
    /**
     * @var \GuzzleHttp\Client
     */
    public $mailService;

    /**
     * Setup guzzle client
     */
    public function setupMailTest()
    {
        $this->mailService = (new \GuzzleHttp\Client(
            ['base_uri' => 'http://127.0.0.1:8025/api/v1/']
        ));


        $this->clearEmails();
    }

    /**
     * Get all the emails
     *
     * @return array
     */
    public function getAllEmails()
    {

        return json_decode($this->mailService->get('messages')->getBody()->getContents());
    }

    /**
     * Get last email sent
     *
     * @return array
     */
    public function getLastEmail()
    {
//        dd(json_decode($this->getAllEmails())[0]->ID);
        $lastEmailId = $this->getAllEmails()[0]->ID;


        return json_decode($this->mailService->get('messages/'.$lastEmailId)->getBody()->getContents());
    }

    /**
     * Delete all emails
     *
     * @return mixed
     */
    public function clearEmails()
    {
        return $this->mailService->delete('messages');
    }

    /**
     * Assert that email body contains the given string
     *
     * @param string $body
     * @param array  $response
     * @param testEnvironment
     */
    public function assertEmailBodyContains($body, $response, $t)
    {
        
//        $emailBody = $response['Content']['Body'];
        $emailBody = $response->Content->Body;
        // Get rid of strange equals character than can break your tests
        $emailBody = str_replace("=\r\n", "", $emailBody);

        $t->assertContains(
            $body,
            $emailBody
        );
    }

    /**
     * Assert that email subject equals the given string
     *
     * @param string $subject
     * @param array  $response
     * @param testEnvironment
     */
    public function assertEmailSubjectIs($subject, $response, $t)
    {
//        $emailSubject = $response['Content']['Headers']['Subject'];
        $emailSubject = $response->Content->Headers->Subject;

        $t->assertTrue(
            in_array(
                $subject,
                $emailSubject
            )
        );
    }

    /**
     * Assert that the email was send to given recipient
     *
     * @param string $recipient
     * @param array  $response
     * @param testEnvironment
     */
    public function assertEmailWasSendTo($recipient, $response, $t)
    {
//        dd($response->Content->Headers->To[0]);
        $emailRecipient = $response->Content->Headers->To;


        $t->assertTrue(
            in_array(
                $recipient,
                $emailRecipient
            )
        );
    }
}

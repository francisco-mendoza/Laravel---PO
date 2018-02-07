<?php
namespace Helper;

use AssertionError;

use GuzzleHttp\Client;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{

    /**
     * @var \GuzzleHttp\Client
     */
    protected $mailService;

    /**
     * Setup guzzle client
     */
    function setupMailTest()
    {
        $this->mailService = (new Client(
            ['base_url' => 'http://127.0.0.1:8025/api/v1/']
        ));

        $this->clearEmails();
    }

    /**
     * Get all the emails
     *
     * @return array
     */
    function getAllEmails()
    {
        return $this->mailService->get('messages')->json();
    }

    /**
     * Get last email sent
     *
     * @return array
     */
    function getLastEmail()
    {
        $lastEmailId = $this->getAllEmails()[0]['ID'];

        return $this->mailService->get('messages/'.$lastEmailId)->json();
    }

    /**
     * Delete all emails
     *
     * @return mixed
     */
    function clearEmails()
    {
        return $this->mailService->delete('messages');
    }

    /**
     * Assert that email body contains the given string
     *
     * @param string $body
     * @param array  $response
     */
    function assertEmailBodyContains($body, $response)
    {
        $emailBody = $response['Content']['Body'];
        // Get rid of strange equals character than can break your tests
        $emailBody = str_replace("=\r\n", "", $emailBody);

        $this->assertContains(
            $body,
            $emailBody
        );
    }

    /**
     * Assert that email subject equals the given string
     *
     * @param string $subject
     * @param array  $response
     */
    function assertEmailSubjectIs($subject, $response)
    {
        $emailSubject = $response['Content']['Headers']['Subject'];

        $this->assertTrue(
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
     */
    function assertEmailWasSendTo($recipient, $response)
    {
        $emailRecipient = $response['Content']['Headers']['To'];

        $this->assertTrue(
            in_array(
                $recipient,
                $emailRecipient
            )
        );
    }

}

<?php

namespace Bendbennett\DemoBundle\Tests\Controller;

class UsersCompaniesControllerTest extends AbstractController
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     * @group UsersCompaniesController::editAction
     */
    public function itShouldReturn200AndUserForUsersCompaniesEditAction()
    {
        $userOneCompanyId = 'abc123';
        $userOne = $this->loadUser('userOne@companyOne.com', 'passwordOne', $userOneCompanyId, ['Director']);

        $client = self::createClient();
        $jwt = $this->login($client, $userOne->getEmail(), 'passwordOne');

        $userOneNewRoles = ['Employee'];

        $client = self::createClient();
        $client->request(
            'PATCH',
            '/users/' . $userOne->getId() . '/companies/' . $userOneCompanyId,
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => "Bearer $jwt"),
            json_encode([
                'roles' => $userOneNewRoles
            ])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $serializerService = $this->bootedTestKernel->getContainer()->get('bendbennett_demo.service.serializer_service');
        $user = $serializerService->deserializeUserFromJson($client->getResponse()->getContent(), 'json', $userOne->getId());

        $this->assertEquals($userOneNewRoles, $user->getUserCompanyById($userOneCompanyId)->getRoles());
    }
}

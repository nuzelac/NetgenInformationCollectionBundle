<?php

namespace Netgen\Bundle\InformationCollectionBundle\Admin\Controller;

use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function listAction(Request $request): Response
    {
        return $this->render('@NetgenInformationCollection/admin/list.html.twig', []);
    }
}

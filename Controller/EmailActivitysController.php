<?php

namespace Redking\Bundle\MailBundle\Controller;


use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Routing\Exception\ResourceNotFoundException,
    Symfony\Component\Validator\Constraints as Assert
    ;

use FOS\RestBundle\View\RouteRedirectView,
    FOS\RestBundle\View\View,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Controller\Annotations,
    FOS\RestBundle\Request\ParamFetcherInterface,
    FOS\RestBundle\Util\Codes;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Redking\Bundle\CoreRestBundle\Controller\BaseRestController,
    Redking\Bundle\CoreRestBundle\Exception\InvalidFormException,
    Redking\Bundle\CoreRestBundle\Handler\BaseHandler;


class EmailActivitysController extends BaseRestController
{

    /**
     * Récupère les Email Activities
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing objects.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="10", description="How many objects to return.")
     * @Annotations\QueryParam(name="sort_field", requirements="\w+", description="Field to use for sorting.", nullable=true)
     * @Annotations\QueryParam(name="sort_order", requirements="^asc|desc$", description="Order of the sort.", nullable=true)
     * @Annotations\QueryParam(name="from_user_id", requirements="\w+", description="Id user émetteur", nullable=true)
     * @Annotations\QueryParam(name="to_user_id", requirements="\w+", description="Id user destinataire", nullable=true)
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     * 
     * @ApiDoc()
     * @Annotations\Get("/email_activities")
     */
    public function getEmailActivitysAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        return parent::getObjectsAction($request, $paramFetcher);
    }

 }

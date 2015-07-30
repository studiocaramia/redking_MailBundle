<?php
/**
 * Controller gérant les webhooks venant de mailgun
 */

namespace Redking\Bundle\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class MailgunHookController extends Controller
{
    /**
     * Récupération des webhooks de mailgun
     * @param  string  $event   [description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function catchAction($event, Request $request)
    {
        $data = $request->request->all();
        $this->checkSignature($data);

        // Récupération de l'activity correspond à ce mail
        $dm = $this->get('doctrine_mongodb')
                    ->getManager();

        $activity = $dm->getRepository('RedkingMailBundle:EmailActivity')
                    ->findOneBy(['message_id' => $data['message-id']]);

        $response = ['success' => false];

        // Mis à jour selon l'event
        if (!is_null($activity)) {
            switch($event) {
                case 'opened':
                    $activity->setOpened(true);
                break;
            }
            
            $dm->persist($activity);
            $dm->flush();

            $response['success'] = true;
        } else {
            $response['message'] = 'Unknown mail';        
        }
        return new JsonResponse($response);
    }

    /**
     * Vérification des données reçues
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function checkSignature($data)
    {
        if (!isset($data['timestamp']) || !isset($data['token']) || !isset($data['signature']) || !isset($data['message-id'])) {
            throw new InvalidParameterException('Missing parameters');
        }

        $phrase = $data['timestamp'].$data['token'];
        $signature = hash_hmac('sha256', $phrase, $this->container->getParameter('mailgun.key'));
        
        if ($signature !== $data['signature']) {
            throw new NotAcceptableHttpException('Invalid signature');
        }
    }

}

<?php

namespace App\Controller\Rest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Player;
use App\Entity\Team;

class PlayerController extends AbstractController
{
    public function getPlayers(Request $request): JsonResponse {
        $playerRepostiory = $this->getDoctrine()->getManager('default')->getRepository(Player::class);
        $arrFilters = $request->query->all();
        $currency = ( !empty($arrFilters['currency']) ) ? $arrFilters['currency'] : false;
        unset($arrFilters['currency']);
        /**
         * @var $playerRepostiory \App\Entity\Player|\App\Repository\PlayerRepository
         */
        $players = $playerRepostiory->findBySomeField($arrFilters);
        if ($currency) {
            $exchange = $playerRepostiory->getCurrencyExchange('EUR', 'USD');
            foreach ($players as $player) {
                $exchangeRate = $exchange->rates->USD;
                $changedAmount = (float)$player->getPrice() * $exchangeRate;
                $player->setPrice($changedAmount);
            }
        }
        return new JsonResponse($players);
    }

    public function getPlayer(Request $request): JsonResponse {
        $playerId = $request->get('player_id');
        if (!$playerId) {
            throw new HttpException(400, "You must set user_id url param");
        }
        $playerRepostiory = $this->getDoctrine()->getManager('default')->getRepository(Player::class);
        return new JsonResponse($playerRepostiory->find($playerId));
    }

    public function postPlayer(Request $request, ValidatorInterface $validator) {
        /**
         * @var $player \App\Entity\Player
         */
        $player = $this->get('serializer')->deserialize(
            $request->getContent(),
            'App\\Entity\\Player',
            'json',
            ['disable_type_enforcement' => true]
        );
        $errors = $validator->validate($player);
        if (count($errors) > 0) {
            $errorData = [
                'status_code' => 400,
                'message' => 'Validation Error',
                'validations' => []
            ];
            foreach ($errors as $validation) {
                $errorData['validations'][$validation->getPropertyPath()] = $validation->getMessage();
            }
            return new JsonResponse($errorData);
        }
        $em = $this->getDoctrine()->getManager('default');
        $teamId = $player->getTeam()->getId();
        $emTeam = $this->getDoctrine()->getManager('default')->getRepository(Team::class);
        $team = $emTeam->find($teamId);
        $player->addChild($team);
        $em->persist($player);
        $em->flush();
        return new RedirectResponse('/v1/es/players/' . $player->getId());
    }

    public function putPlayer(Request $request, ValidatorInterface $validator) {
        /**
         * @var $player \App\Entity\Player
         */
        $player = $this->get('serializer')->deserialize(
            $request->getContent(),
            'App\\Entity\\Player',
            'json',
            ['disable_type_enforcement' => true]
        );
        $playerId = $request->get('player_id');
        $errors = $validator->validate($player);
        if (count($errors) > 0) {
            $errorData = [
                'status_code' => 400,
                'message' => 'Validation Error',
                'validations' => []
            ];
            foreach ($errors as $validation) {
                $errorData['validations'][$validation->getPropertyPath()] = $validation->getMessage();
            }
            return new JsonResponse($errorData);
        }
        $emPlayers = $this->getDoctrine()->getManager('default')->getRepository(Player::class);
        /**
         * @var $playerPersistObject \App\Entity\Player
         */
        $playerPersistObject = $emPlayers->find($playerId);

        if (empty($playerPersistObject)) {
            throw new HttpException(404, 'player_id not found');
        }

        $playerPersistObject->setName($player->getName());
        $playerPersistObject->setAge($player->getAge());
        $playerPersistObject->setPosition($player->getPosition());
        $playerPersistObject->setPrice($player->getPrice());
        $playerPersistObject->setPrice($player->getPrice());

        $emTeam = $this->getDoctrine()->getManager('default')->getRepository(Team::class);
        $teamPersisObject = $emTeam->find($player->getTeam()->getId());

        if (empty($teamPersisObject)) {
            throw new HttpException(404, 'Team id not found');
        }

        $playerPersistObject->addChild($teamPersisObject);
        $emSave = $this->getDoctrine()->getManager('default');
        $emSave->persist($playerPersistObject);
        $emSave->flush();
        return new JsonResponse(null, 204);
    }

    public function deletePlayer(Request $request) {
        $playerId = $request->get('player_id');
        $em = $this->getDoctrine()->getManager('default');
        $playerToDeleteObjet = $em->find(Player::class, $playerId);
        if (empty($playerToDeleteObjet)) {
            throw new HttpException(404, "Player doesnt exists");
        }
        $em->remove($playerToDeleteObjet);
        $em->flush();
        return new JsonResponse(null, 204);
    }
}
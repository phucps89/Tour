<?php

/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 1/1/2017
 * Time: 6:33 PM
 */
class ApiController extends BaseController
{
    public function index(){
        $adviceDetailTableName = AdviceDetail::getTableName();
        $adviceTableName = Advice::getTableName();
        $advices = Advice
            ::leftJoin(
                $adviceDetailTableName,
                $adviceDetailTableName . '.id_advice',
                '=',
                $adviceTableName . '.id'
            )
            ->groupBy($adviceTableName . '.id')
            ->select([
                $adviceTableName . '.*',
                DB::raw("COUNT(DISTINCT $adviceDetailTableName.id_question) as sumQuestion")
            ])
            ->get();
        $sumQuestion = Question::count();
        return Response::json([
            'advices'     => $advices,
            'sumQuestion' => $sumQuestion
        ]);
    }

    public function login(){
        $credentials = Request::only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return Response::json(['msg' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return Response::json(['msg' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return Response::json(compact('token'));
    }
}
<?php


namespace Filesharing\Services;


use Symfony\Component\Validator\Constraints\EqualTo;

class CsrfService
{
    private $token;

    private $validator;

    /**
     * CsrfService constructor.
     * @param $validator
     */
    public function __construct($validator)
    {
        $this->validator = $validator;
    }


    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    public function setCookieToken($response, $token)
    {
        $time = time();
        if (is_null($token)) {
            $token = bin2hex(random_bytes(20));
            $this->token = $token;
            return $response->withHeader("set-cookie", "token=$token;max-age=$time+60*60");
        } else {
            $this->token = $token;
            return $response->withHeader("set-cookie", "token=$token;max-age=$time+60*60");
        }
    }

    public function checkToken($request)
    {
        $ptoken = is_null($request->getParsedBodyParam('token')) ? '' : $request->getParsedBodyParam('token');
        $ctoken = is_null($request->getCookieParam('token')) ? '' : $request->getCookieParam('token');
        return $this->validator->validate($ptoken, new EqualTo([
            'value' => $ctoken,
            'message' => "Error"
        ]));
    }

}
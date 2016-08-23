<?php
namespace Project\FrontBundle\Form\Select2;

use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class AjaxChoiceResponse
 * @package Project\FrontBundle\Form\Select2
 */
class AjaxChoiceResponse extends JsonResponse
{
    /**
     * @var callable
     */
    private $callable;
    /**
     * @var bool
     */
    private $more;

    /**
     * Constructor.
     *
     * @param callable $callable Transform object to a select label
     * @param mixed    $data     The response data
     * @param bool     $more
     * @param int      $status   The response status code
     * @param array    $headers  An array of response headers
     */
    public function __construct($callable, $data = null, $more = false, $status = 200, $headers = [])
    {
        if (false === is_callable($callable)) {
            throw new UnexpectedTypeException($callable, "callable");
        }
        $this->callable = $callable;
        $this->more     = $more;
        parent::__construct($data, $status, $headers);

    }

    public function setData($data = [])
    {

        $response = [];
        $p        = PropertyAccess::createPropertyAccessor();

        if ($data instanceof AbstractPagination) {
            /** @var SlidingPagination $data */
            $data = $data->getItems();
        }
        foreach ($data as $d) {
            $response[] = [
                "id"   => $p->getValue($d, is_array($d) ? "[id]" : "id"),
                "text" => call_user_func($this->callable, $d),
            ];
        }

        if (empty($data) || empty($response)) {
            parent::setData(["results" => []]);

            return;
        }
        parent::setData(
            [
                "results"    => $response,
                "pagination" => ["more" => $this->more],
            ]
        );
    }
}

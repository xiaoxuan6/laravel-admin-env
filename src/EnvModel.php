<?php
/**
 * Date: 2019/2/22
 * Time: 17:52
 */
namespace James\Env;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class EnvModel extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }


    public function paginate()
    {
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $key = Request::get('key', '');
        $start = ($page - 1) * $perPage;
        $data = $this->getEnv('', $key);
        $list = array_slice($data, $start, $perPage);
        $list = static::hydrate($list);
        $paginator = new LengthAwarePaginator($list, count($data), $perPage);
        $paginator->setPath(url()->current());
        return $paginator;
    }

    public static function with($relations)
    {
        return new static;
    }

    public function findOrFail($id)
    {
        $item = $this->getEnv($id);
        return static::newFromBuilder($item);
    }


    public function save(array $options = [])
    {
        $data = $this->getAttributes();

        return $this->setEnv([$data['key'] => $data['value']]);
    }

    /**
     * Get .env variable.
     * @param null $id
     * @return array|mixed
     */
    private function getEnv($id = null, $key = null)
    {
        $string = $this->getEnvFile()->toArray();
        $string = array_filter($string);

        $array = [];
        foreach ($string as $keys => $v) {
            $str = explode('=', $v, 2);
            if(count($str) == 2){
                $array[] = [
                    'id'    => $keys + 1,
                    'key'   => $str[0],
                    'value' => $str[1],
                ];
            }
        }

        if ($id) {
            $data = collect($array)->where('id', $id)->toArray();
            return $data ? current($data) : [];
        } elseif ($key) {
            $data = collect($array)->where('key', $key)->toArray();
            return $data ? $data : [];
        } else
            return $array;
    }

    /**
     * Update or create .env variable.
     * @param $key
     * @param $value
     * @return bool
     */
    private function setEnv(array $data)
    {
        $string = "";
        $contentArray = $this->getEnvFile();
        $content = $contentArray->transform(function ($item) use ($data, &$string) {
            foreach ($data as $key => $value) {
                if (str_contains($item, $key)) {
                    return $key . '=' . $value;
                }else{
                    $string = $key . '=' . $value;
                }
            }
            return $item;
        });

        $content = $content->contains($string) === false ? $content->push($string) : $content;

        $content = implode($content->toArray(), "\n");

        \File::put(self::getEnvFilePath(), $content);
        return true;
    }

    /**
     * Delete .env variable
     * @param $id
     * @return bool
     */
    protected function isDel($id)
    {
        $data = $this->getEnv();
        $old_ids = array_column($data, 'id');

        if (!is_array($id))
            $ids = [$id];
        else
            $ids = $id;

        $contentArray = $this->getEnvFile();
        foreach ($ids as $value) {
            $index = array_search($value, $old_ids);
            if ($index === false)
                continue;
            $string = $data[$index]['key']."=".$data[$index]['value'];
            $contentArray = $contentArray->filter(function($item) use ($string){
                return $item != $string;
            });
        }

        $content = implode($contentArray->toArray(), "\n");

        \File::put(self::getEnvFilePath(), $content);

        return true;
    }

    /**
     * 获取.env
     * @return string
     */
    private static function getEnvFilePath()
    {
        return Container::getInstance()->environmentPath() . DIRECTORY_SEPARATOR .
            Container::getInstance()->environmentFile();
    }

    /**
     * Notes: 获取 .env 内容
     * Date: 2019/6/5 14:36
     * @return array
     */
    private static function getEnvFile()
    {
        return collect(file(self::getEnvFilePath(), FILE_IGNORE_NEW_LINES));
    }
}
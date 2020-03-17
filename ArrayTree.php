<?php
/**
 * ����������ͨ���黥ת���
 */
class ArrayTree
{
    /**
     * @var array tree2array��������ת��Ϊ��ͨ��������
     */
    protected $tree2array = [];

    /**
     * @var array tree2array_two��������ת��Ϊ��ͨ��������
     */
    protected $tree2array_two = [];

    /**
     * ����ͨ����ת��Ϊ������
     *
     * @param array $array ��ת������
     * @param bool $preserveKeys **true**ʱ����ԭ����������ϵ
     * @param string $idField
     * @param string $parentField
     * @return array ת�����������
     */
    public function array2tree(array $array, $preserveKeys = false, $idField = 'id', $parentField = 'parent_id')
    {
        $array  = app()->helper->arr->toArray($array);
        $tree   = [];
        $refer  = [];

        foreach ($array as $key => $data) {
            if (!isset($data[$idField])) {
                continue;
            }
            $id = $data[$idField];
            $array[$key]['treeInfo']['leaf'] = true;
            $refer[$id] = &$array[$key];
        }

        foreach ($array as $key => $data) {
            if (!isset($data[$parentField])) {
                continue;
            }

            $parentId = $data[$parentField];

            if ($parentId && isset($refer[$parentId])) {
                $parent = &$refer[$parentId];
                $parent['treeInfo']['leaf'] = false;

                if ($preserveKeys) {
                    $parent['children'][$key] = &$array[$key];
                } else {
                    $parent['children'][] = &$array[$key];
                }
            } elseif ($preserveKeys) {
                $tree[$key] = &$array[$key];
            } else {
                $findTrace = false;
                $nodes = explode(',', $data['node'] ?? '');
                foreach ($nodes as $n) {
                    if ($data[$parentField] && isset($refer[$n])) {
                        $parent = &$refer[$n];
                        $parent['treeInfo']['leaf'] = false;
                        $parent['children'][] = &$array[$key];
                        $findTrace = true;
                        break;
                    }
                }
                if (!$findTrace) {
                    $tree[] = &$array[$key];
                }
            }
            unset($array[$key]);
        }

        return $tree;
    }

    /**
     * ��������ת��Ϊ��ͨ����
     *
     * @param array $tree ��ת��������
     * @param bool $idAsKey **true**ʱid��ֵ��Ϊ������������
     * @param Closure $map
     * @param int $level ��Σ�Ĭ��0
     * @param string $idField
     *
     * @return array ת�������ͨ����
     */
    function tree2array(array $tree, $idAsKey = false, $idField = 'id', Closure $map = null, $level = 0)
    {
        $tree = app()->helper->arr->toArray($tree);
        if (0 == $level) {
            $this->tree2array = [];
        }
        foreach ($tree as $key => $value) {
            $children = isset($value['children']) ? $value['children'] : null;
            $value['treeInfo']['level'] = $level + 1;
            unset($value['children']);

            if ($map) {
                call_user_func_array($map, [$key, &$value, $value['treeInfo']['level']]);
            }

            if ($idAsKey) {
                $this->tree2array[$value[$idField]] = $value;
            } else {
                $this->tree2array[] = $value;
            }

            if ($children) {
                $this->tree2array($children, $idAsKey, $idField, $map, $level + 1);
            }
        }

        return $this->tree2array;
    }

    /**
     * ��������ת��Ϊ��ͨ����
     *
     * @param array $tree ��ת��������
     * @param bool $idAsKey **true**ʱid��ֵ��Ϊ������������
     * @param string $idField
     * @param Closure $map
     * @param int $level ��Σ�Ĭ��0
     * @param string $childKey
     *
     * @return array ת�������ͨ����
     */
    function tree2array_two(array $tree, $idAsKey = false, $idField = 'id', Closure $map = null, $level = 0, $childKey = 'children')
    {
        $tree = app()->helper->arr->toArray($tree);
        if (0 == $level) {
            $this->tree2array_two = [];
        }
        foreach ($tree as $key => $value) {
            $children = isset($value[$childKey]) ? $value[$childKey] : null;
            $value['treeInfo']['level'] = $level + 1;
            unset($value[$childKey]);

            if ($map) {
                call_user_func_array($map, [$key, &$value, $value['treeInfo']['level']]);
            }

            if ($idAsKey) {
                $this->tree2array_two[$value[$idField]] = $value;
            } else {
                $this->tree2array_two[] = $value;
            }

            if ($children) {
                $this->tree2array_two($children, $idAsKey, $idField, $map, $level + 1);
            }
        }

        return $this->tree2array_two;
    }

    /**
     * ����ͨ����ת��Ϊ������
     *
     * @param array $data ��ת������
     * @param string $idField
     * @param string $parentField
     * @return array ת�����������
     */
    public function addNode(array $data, $idField = 'id', $parentField = 'parent_id')
    {
        $result = [];
        foreach ($data as $key => $item) {
            $id = $item[$idField];
            $result[$id] = $item;
            if (0 == $item[$parentField]) {
                $result[$id]['node'] = $id;
                unset($data[$key]);
            }
        }
        while (count($data)) {
            foreach ($data as $key => $item) {
                $id = $item[$idField];
                $parentId = $item[$parentField];
                
                if (!isset($result[$parentId])) {
                    die("��id#{$parentId}������")
                }
                if (isset($result[$parentId]['node'])) {
                    $item['node'] = $result[$parentId]['node'] . ',' . $id;
                    $result[$id] = $item;
                    unset($data[$key]);
                }
            }
        }
        return $result;
    }

    /**
     * ����ͨ����ת��Ϊ������
     *
     * @param array $data ��ת������
     * @param string $idField
     * @param string $parentField
     * @return array ת�����������
     */
    public function addNodeNew(array $data, $idField = 'id', $parentField = 'parent_id')
    {
        $data = array_combine(array_column($data, $idField) , $data);

        foreach ($data as $key => &$val) {
            if (isset($val[$idField])) {
                $val['node'][] = $val[$idField];
            }
            if (isset($val[$parentField]) && $val[$parentField]) {
                $val['node'][] = $val[$parentField];
                $val['node'][] = &$data[$val[$parentField]]['node'];
            }
        }
        unset($val);

        foreach ($data as &$val) {
            $nodes = [];
            static::nodeIterator($val['node'], $nodes);
            $nodes = array_reverse(array_unique($nodes));
            $val['node_new'] = implode(',', $nodes);

            unset($nodes);
        }

        foreach ($data as &$val) {
            $val['node'] = $val['node_new'];
            unset($val['node_new']);
        }

        return $data;
    }

    /**
     * �ڵ��������
     *
     * @param $nodes
     * @param $data
     */
    private static function nodeIterator($nodes, &$data){
        if (is_array($nodes)) {
            foreach ($nodes as $val) {
                if (is_array($val)) {
                    static::nodeIterator($val, $data);
                } else {
                    $val && ($data[] = $val);
                }
            }
        } else {
            $nodes && ($data[] = $nodes);
        }
    }
}
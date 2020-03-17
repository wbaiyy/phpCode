<?php
  /**
     * 更新节点关系
     *
     * @param array $old 
     * @param array $new
     * @return bool|int|string
     */
    public static function updateNode(array $old, array $new)
    {
        $old = new ArrayObject($old, ArrayObject::ARRAY_AS_PROPS);
        $new = new ArrayObject($new, ArrayObject::ARRAY_AS_PROPS);

        // 新增
        if (!$old->count()) {
            return static::updateNodeInternal($new->id, $new->parent_id);
        } elseif ($old->parent_id == $new->parent_id) {
            return true;
        } else {
            $node = static::updateNodeInternal($old->id, $new->parent_id);
        }

        $len = strlen($old->node);
        /** @var \stdClass[] $items */
        $items = static::find()->select('id,node,name')
            ->where('node LIKE :node', ['node' => $old['node'] . ',%'])
            ->indexBy('id')
            ->all();
        foreach ($items as $id => $item) {
            $newNode = substr_replace($item->node, $node, 0, $len);
            static::updateAll(['node' => $newNode], ['id' => $id]);
        }
        $items[$old->id] = $old->id;
        static::clearCache(array_keys($items));
        return $node;
    }

    /**
     * 更新节点
     *
     * @param int $id
     * @param int $parentId
     * @return string
     */
    protected static function updateNodeInternal($id, $parentId)
    {
        // 节点等于自身id
        if (0 == $parentId) {
            $node = $id;
        } else {
            $node = static::find()->where(['id' => $parentId])->select('node')->one()->node;
            $node .= ',' . $id;
        }
        static::updateAll(['node' => $node], ['id' => $id]);
        return $node;
    }


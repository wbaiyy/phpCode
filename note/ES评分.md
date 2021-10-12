# 评分相关

- [根据字段返回值提升score](https://www.elastic.co/guide/cn/elasticsearch/guide/current/boosting-by-popularity.html#_boost_mode)
	
	在搜索时，可以将 `function_score` 查询与 `field_value_factor` 结合使用，即将点赞数与全文相关度评分结合 

	```
	GET /blogposts/post/_search
	{
	  "query": {
		"function_score": { 
		  "query": { 
			"multi_match": {
			  "query":    "popularity",
			  "fields": [ "title", "content" ]
			}
		  },
		  "field_value_factor": { 
			"field": "votes",   //默认：new_score = old_score * number_of_votes
			"modifier": "log1p",  //可选；此时new_score = old_score * log(1 + number_of_votes)
			"factor": 2,  //对字段加权,可以为小数，此时new_score = old_score * log(1 + factor * number_of_votes)
		  },
		  "boost_mode": "sum", // boost_mode 来控制函数与查询评分 _score 合并后的结果, 此时new_score = old_score + log(1 + 0.1 * number_of_votes)
		  "max_boost":  1.5 //无论 field_value_factor 函数的结果如何，最终结果都不会大于 1.5 。
		}
	  }
	}
	```
	- modi0fier：`none` （默认状态）、 `log` 、 `log1p` 、 `log2p` 、 `ln` 、 `ln1p` 、 `ln2p` 、 `square` 、 `sqrt` 以及 `reciprocal`
	
	- boost_mode：`multiply`评分 _score 与函数值的积（默认）,·sum·-评分 _score 与函数值的和,`max`-评分 _score 与函数值间的较大值,`min`-评分 _score 与函数值间的较小值,`replace`-函数值替代评分 _score


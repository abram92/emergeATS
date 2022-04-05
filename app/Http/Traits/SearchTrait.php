<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Search;
use App\Auth;
use App\Alias;

use DB;

trait SearchTrait
{
	
	protected function getSavedSearch($searchid)
	{
		$search = Search::findOrFail($searchid);
		if ($search->user_id != auth()->user()->id)
			return [[], false];
		return [json_decode($search->parameters, true), $search->description != ''];
	}
	
	protected function storeSearch($queryArray, $queryFilter, $type)
	{
		$searchid = null;
		$params = $this->removeEmptyCriteria( $queryArray);
			if (!empty($params)){
				$param['user_id'] = auth()->user()->id;
				$param['search_type'] = $type;
				$param['parameters'] = json_encode($params);
				$param['filtercriteria'] = json_encode($queryFilter);
				
				$search = Search::create($param);
				$searchid = $search->id;
			}
		return $searchid;
	}

	protected function deleteSearch($searchid)
	{
		$searchid = null;
			if ($query){
				$s['user_id'] = auth()->user()->id;
				$s['search_type'] = 'client';
				$s['parameters'] = json_encode($query);
				
				$search = Search::create($s);
				$searchid = $search->id;
			}
		return $searchid;
	}
	
	/*
	protected function saveSearch($searchid, $description)
	{
		$searchid = null;
			if ($query){
				$s['user_id'] = auth()->user()->id;
				$s['search_type'] = 'client';
				$s['parameters'] = json_encode($query);
				
				$search = Search::create($s);
				$searchid = $search->id;
			}
		return $searchid;
	}
*/

	protected function FTS_setLongTextQueryString($str)
	{
		$dbDriver = $this->getDatabaseDriver();
		$fieldVal = $this->FTS_getAliases($str, $dbDriver);
		$fieldVal = $this->FTS_mapSpecialChars($fieldVal, $dbDriver);
		return $this->FTS_sanitiseExpression($fieldVal, $dbDriver);
	}

	protected function FTS_getFullTextClauseSyntax($textquery) {
		$dbDriver = $this->getDatabaseDriver();
		if ($dbDriver == 'pgsql') {	
			return DB::raw("chunk_tokens @@ to_tsquery(".$textquery.") = 'true'");
		}
		if ($dbDriver == 'sqlsrv') {	
			return DB::raw("contains (search_transl, '".$textquery."')");
		}
	}
	
	protected function FTS_getFullTextRank($textquery, $class, $field) {
	
//		return DB::table('long_full_texts')
//                     ->select(DB::raw('id, longtextable_id, longtextable_type, field_type, ts_rank_cd(chunk_tokens, query, 32 /* rank/(rank+1) */ ) AS rank'))
//					 ->join("to_tsquery('".$textquery."') query", "query @@ chunk_tokens")
//                    ->where('longtextable_type', $class)
//					 ->where('field_type', $field)
//                     ->get(); */
//	return collect(DB::select("SELECT id, longtextable_id, longtextable_type, field_type, ts_rank_cd(chunk_tokens, query, 32 /* rank/(rank+1) */ ) AS rank
		$dbDriver = $this->getDatabaseDriver();
		if ($dbDriver == 'pgsql') {		
			return collect(DB::select("SELECT id, longtextable_id, longtextable_type, field_type, (ts_rank_cd(chunk_tokens, query, 32 /* rank/(rank+1) */ ))/(MAX(ts_rank_cd(chunk_tokens, query, 32 /* rank/(rank+1) */ )) OVER( PARTITION BY 1))*100 AS rank
			FROM long_full_texts, to_tsquery(".$textquery.") query
			WHERE  query @@ chunk_tokens
			and longtextable_type = '".$class."' and field_type = '".$field."'"))->pluck('rank', 'longtextable_id');
		}
		
		if ($dbDriver == 'sqlsrv') {		
			return collect(DB::select("select [key] as id, longtextable_id, longtextable_type, field_type, rank, LOG(RANK)/log(MAX(RANK) OVER( PARTITION BY 1))*100 as percentage from containstable(long_full_texts, search_transl, '".$textquery."'), long_full_texts where [KEY] = long_full_texts.id and longtextable_type = '".$class."' and field_type = '".$field."'"))->pluck('percentage', 'longtextable_id');
		}
		return collect([]);
	}
	
	protected function FTS_mapSpecialChars($txt, $dbDriver='') {
		if (!$dbDriver)
			$dbDriver = $this->getDatabaseDriver();
		if ($dbDriver == 'pgsql') {		
			$pattern = ['/([c])\+\+/i', '/([a-z])#/i', '/\.net/i'];
			$replacement = ['${1}plusCharplusChar', '${1}hashChar', 'dotCharnet'];
			return preg_replace($pattern, $replacement, $txt);
		}	
		
		if ($dbDriver == 'sqlsrv') {		
			for($i=0; $i<strlen($txt); $i++){
				if (preg_match("([\+\#\*\.@])", $txt[$i])) {
					if($i+1<=strlen($txt)){ 
						if($i+1 < strlen($txt))
							$substitute = !((ord($txt[$i])==46)&&(ord($txt[$i+1])==32));
						else
							$substitute = true;  
						if ($substitute)
							$txt = substr_replace($txt, (string)ord($txt[$i]), $i, 1);
					}
				}
			}
//			return $txt;
		}
		return $txt;
	}


	private function FTS_formatPhrase($string, $dbDriver)
	{
		if ($dbDriver == 'pgsql') {
			$string = str_ireplace("'", "\'",$string);
			$string = preg_replace('/\s{1,}/', ' ', trim($string));
			$string = str_ireplace(" ", " <-> ", $string);			
		}
		return $string;
	}

	private function FTS_sanitiseExpression($string, $dbDriver)
	{
		if ($dbDriver == 'pgsql') {
//			$string = str_ireplace("'", "\'",$string);
			$string = str_ireplace(" AND ", " & ",$string);
			$string = str_ireplace(" OR ", " | ",$string);
			$string = str_ireplace("\"", "'",$string);
		}
		$string = app('db')->getPdo()->quote($string);
		return $string;
	}

	private function FTS_getAliases($str, $dbDriver)
	{
		$keywords = preg_split( "/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|[\s,]+|([(])|([)])/", $str, 0, PREG_SPLIT_DELIM_CAPTURE );
		$keywords = array_merge(array_diff($keywords, array("")));
		$ret = "";
		$concat = false;
		foreach ($keywords as $k => $v) {
			if ((strtolower($v) == "and") || (strtolower($v) == "or")) {
				$ret .= " $v ";
				if ($k > 0)
					$concat = true;
			} else if ((strtolower($v) == "(") || (strtolower($v) == ")")) {
				$ret .= "$v";
				if (strtolower($v) == "(")
					$concat = true;
			} else {
				$alias = Alias::whereHas('keywords', function ($query) use ($v) {
					$query->where('keyword', '=', $v);
				})->with('keywords')->first();
				if ($alias && $alias->keywords->count()) {

					$arrayList = $alias->keywords->pluck('keyword')->toArray();
					foreach ($arrayList as $k1 => $v1) {
						$arrayList[$k1] = $this->FTS_formatPhrase($v1, $dbDriver);
					}
					$phrases = "(\"".implode('" or "', $arrayList)."\")";
				} else 
					$phrases = "\"".$this->FTS_formatPhrase($v, $dbDriver)."\"";
				if (($k > 0) && (!$concat))
					$ret .= " or $phrases ";
				else      
					$ret .= $phrases." ";   
				$concat = false;
			}
		}
		return $ret;
	}
	
	private function removeEmptyCriteria($input) { 
		foreach ($input as &$value) { 
			if (is_array($value)) { 
				$value = $this->removeEmptyCriteria($value); 
			}
		}

		return array_filter($input, function($item){
			return $item !== null && $item !== '' && !(is_array($item) && empty($item));
		}); 
	}
	
	private function clientContactConditional($contactQuery, $name, $position, $phone, $email, &$queryFilter) {
		if ($name) {
			$contactQuery->where(DB::raw($this->searchFullnameFields()), 'ILIKE', '%'.str_replace(' ', '%', $name).'%');
			$queryFilter['Contact Name'] = $name;
		}
		if ($position) {
			$contactQuery->where('position', 'ILIKE', '%'.str_replace(' ', '%', $position).'%');
			$queryFilter['Contact Position'] = $position;
		}
		if ($phone || $email) {
			$contactQuery->whereHas("contactfields", function ($q1) use ($phone, $email, &$queryFilter) {
				if ($phone)	{
					$q1->where('data', 'ILIKE', '%'.$phone.'%')->whereIn('contact_field_type_id', [2,3]);
					$queryFilter['Contact Phone'] = $phone;
				}
				if ($email) {
					$q1->where('data', 'ILIKE', '%'.$email.'%')->where('contact_field_type_id', 1);
					$queryFilter['Contact Email'] = $email;
				}
			});
		}
	}


	protected function getRegexConditional($field, $value, $caseinsensitive=false, $negate=false) {
		$dbDriver = $this->getDatabaseDriver();
		if ($dbDriver == 'pgsql') {	
		    $casestr = $caseinsensitive ? "*" : "";
			$negstr = $negate ? "!" : "";
			return [[DB::raw($field), $negstr."~".$casestr, $value]];
		}
		if ($dbDriver == 'sqlsrv') {	
		    $casestr = $caseinsensitive ? "ilike" : "like";
			$negstr = $negate ? "not " : "";
			return [[DB::raw("lower($field)"), $negstr.$casestr, $textquery]];
		}
	}
	
	protected function convertTextareaToArray($value) {
		$arr = preg_split("/\r\n|\n|\r/", $value);
		$arr = array_map('trim', $arr);
		$arr = array_diff($arr, array(""));
		return $arr;
	}
	
	private function searchFullnameFields() {
		return "CONCAT(firstname, ' ',lastname)";
	}
	
	
	private function getDatabaseDriver() {
		$connection = config('database.default');
		return config("database.connections.{$connection}.driver");
	}
	
	
}

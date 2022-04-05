<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLongFullTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('long_full_texts', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->text('chunk');
            $table->unsignedInteger('editor_id')->nullable();
			$table->text('search_transl');
			$table->string('field_type');
			$table->morphs('longtextable');
            $table->timestamps();
			
			$table->foreign('editor_id')->references('id')->on('users')->onDelete('cascade');
			
        });
		$connection = config('database.default');
		$dbDriver = config("database.connections.{$connection}.driver");
		if ($dbDriver == 'pgsql') {
			DB::statement("ALTER TABLE long_full_texts ADD COLUMN chunk_tokens TSVECTOR");
			DB::statement("UPDATE long_full_texts SET chunk_tokens = to_tsvector('english', search_transl)");
			DB::statement("CREATE INDEX chunk_tokens_gin ON long_full_texts USING GIN(chunk_tokens)");
			DB::statement("CREATE TRIGGER ts_chunk_tokens BEFORE INSERT OR UPDATE ON long_full_texts FOR EACH ROW EXECUTE PROCEDURE tsvector_update_trigger('chunk_tokens', 'pg_catalog.english', 'search_transl')");
		}
		if ($dbDriver == 'sqlsvr') {
//			IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
//begin
//EXEC [mymatrix].[dbo].[sp_fulltext_database] @action = 'enable'
//end
			DB::statement("CREATE FULLTEXT INDEX ON long_full_texts(search_transl)   
   KEY INDEX ui_LongTextTransl   
   WITH STOPLIST = SYSTEM;  
GO  ");
		}		
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		$connection = config('database.default');
		$dbDriver = config("database.connections.{$connection}.driver");
		if ($dbDriver == 'pgsql') {
			DB::statement("DROP TRIGGER IF EXISTS tsvector_update_trigger ON long_full_texts");
			DB::statement("DROP INDEX IF EXISTS chunk_tokens_gin");
			DB::statement("ALTER TABLE long_full_texts DROP COLUMN chunk_tokens");
		}
         Schema::dropIfExists('long_full_texts');
    }
}

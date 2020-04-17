<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPageSitemapColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('pages', 'sitemap_change')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('sitemap_change', 10)->nullable();
            });
        }

        if (!Schema::hasColumn('pages', 'sitemap_priority')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->decimal('sitemap_priority', 1,1)->default(0.5);
            });
        }

        if (!Schema::hasColumn('entries', 'sitemap_change')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->string('sitemap_change', 10)->nullable();
            });
        }

        if (!Schema::hasColumn('entries', 'sitemap_priority')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->decimal('sitemap_priority', 1,1)->default(0.5);
            });
        }

        if (!Schema::hasColumn('entry_types', 'sitemap_change')) {
            Schema::table('entry_types', function (Blueprint $table) {
                $table->string('sitemap_change', 10)->nullable();
            });
        }

        if (!Schema::hasColumn('entry_types', 'sitemap_priority')) {
            Schema::table('entry_types', function (Blueprint $table) {
                $table->decimal('sitemap_priority', 1,1)->default(0.5);
            });
        }

        if (!Schema::hasColumn('taxonomy_types', 'sitemap_change')) {
            Schema::table('taxonomy_types', function (Blueprint $table) {
                $table->string('sitemap_change', 10)->nullable();
            });
        }

        if (!Schema::hasColumn('taxonomy_types', 'sitemap_priority')) {
            Schema::table('taxonomy_types', function (Blueprint $table) {
                $table->decimal('sitemap_priority', 1,1)->default(0.5);
            });
        }

        if (!Schema::hasColumn('taxonomies', 'sitemap_change')) {
            Schema::table('taxonomies', function (Blueprint $table) {
                $table->string('sitemap_change', 10)->nullable();
            });
        }

        if (!Schema::hasColumn('taxonomies', 'sitemap_priority')) {
            Schema::table('taxonomies', function (Blueprint $table) {
                $table->decimal('sitemap_priority', 1,1)->default(0.5);
            });
        }

        if (!Schema::hasColumn('event_slugs', 'sitemap_change')) {
            Schema::table('event_slugs', function (Blueprint $table) {
                $table->string('sitemap_change', 10)->nullable();
            });
        }

        if (!Schema::hasColumn('event_slugs', 'sitemap_priority')) {
            Schema::table('event_slugs', function (Blueprint $table) {
                $table->decimal('sitemap_priority', 1,1)->default(0.5);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('pages','sitemap_priority')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('sitemap_priority');
            });
        }
        if (Schema::hasColumn('pages','sitemap_change')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('sitemap_change');
            });
        }

        if (Schema::hasColumn('entries','sitemap_priority')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->dropColumn('sitemap_priority');
            });
        }
        if (Schema::hasColumn('entries','sitemap_change')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->dropColumn('sitemap_change');
            });
        }

        if (Schema::hasColumn('entry_types','sitemap_priority')) {
            Schema::table('entry_types', function (Blueprint $table) {
                $table->dropColumn('sitemap_priority');
            });
        }
        if (Schema::hasColumn('entry_types','sitemap_change')) {
            Schema::table('entry_types', function (Blueprint $table) {
                $table->dropColumn('sitemap_change');
            });
        }

        if (Schema::hasColumn('taxonomy_types','sitemap_priority')) {
            Schema::table('taxonomy_types', function (Blueprint $table) {
                $table->dropColumn('sitemap_priority');
            });
        }
        if (Schema::hasColumn('taxonomy_types','sitemap_change')) {
            Schema::table('taxonomy_types', function (Blueprint $table) {
                $table->dropColumn('sitemap_change');
            });
        }

        if (Schema::hasColumn('taxonomies','sitemap_priority')) {
            Schema::table('taxonomies', function (Blueprint $table) {
                $table->dropColumn('sitemap_priority');
            });
        }
        if (Schema::hasColumn('taxonomies','sitemap_change')) {
            Schema::table('taxonomies', function (Blueprint $table) {
                $table->dropColumn('sitemap_change');
            });
        }

        if (Schema::hasColumn('event_slugs','sitemap_priority')) {
            Schema::table('event_slugs', function (Blueprint $table) {
                $table->dropColumn('sitemap_priority');
            });
        }
        if (Schema::hasColumn('event_sligs','sitemap_change')) {
            Schema::table('event_sugs', function (Blueprint $table) {
                $table->dropColumn('sitemap_change');
            });
        }
    }
}

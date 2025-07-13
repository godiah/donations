<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contributions', function (Blueprint $table) {
            // Add CyberSource specific fields
            $table->string('cybersource_transaction_uuid')->nullable()->after('cybersource_transaction_id');
            $table->string('cybersource_reference_number')->nullable()->after('cybersource_transaction_uuid');
            $table->string('cybersource_auth_code')->nullable()->after('cybersource_reference_number');
            $table->string('cybersource_decision')->nullable()->after('cybersource_auth_code');
            $table->string('cybersource_reason_code')->nullable()->after('cybersource_decision');
            $table->text('cybersource_signed_field_names')->nullable()->after('cybersource_reason_code');
            $table->text('cybersource_signature')->nullable()->after('cybersource_signed_field_names');
            $table->timestamp('cybersource_signed_date_time')->nullable()->after('cybersource_signature');

            // Billing information for CyberSource
            $table->string('bill_to_forename')->nullable()->after('email');
            $table->string('bill_to_surname')->nullable()->after('bill_to_forename');
            $table->string('bill_to_address_line1')->nullable()->after('bill_to_surname');
            $table->string('bill_to_address_city')->nullable()->after('bill_to_address_line1');
            $table->string('bill_to_address_state')->nullable()->after('bill_to_address_city');
            $table->string('bill_to_address_postal_code')->nullable()->after('bill_to_address_state');
            $table->string('bill_to_address_country', 2)->default('KE')->after('bill_to_address_postal_code');
        });

        Schema::table('transactions', function (Blueprint $table) {
            // Add CyberSource specific transaction fields
            $table->string('cybersource_transaction_uuid')->nullable()->after('gateway_transaction_id');
            $table->string('cybersource_reference_number')->nullable()->after('cybersource_transaction_uuid');
            $table->string('cybersource_auth_code')->nullable()->after('cybersource_reference_number');
            $table->string('cybersource_decision')->nullable()->after('cybersource_auth_code');
            $table->string('cybersource_reason_code')->nullable()->after('cybersource_decision');
            $table->string('cybersource_payment_token')->nullable()->after('cybersource_reason_code');
        });
    }

    public function down()
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropColumn([
                'cybersource_transaction_uuid',
                'cybersource_reference_number',
                'cybersource_auth_code',
                'cybersource_decision',
                'cybersource_reason_code',
                'cybersource_signed_field_names',
                'cybersource_signature',
                'cybersource_signed_date_time',
                'bill_to_forename',
                'bill_to_surname',
                'bill_to_address_line1',
                'bill_to_address_city',
                'bill_to_address_state',
                'bill_to_address_postal_code',
                'bill_to_address_country'
            ]);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'cybersource_transaction_uuid',
                'cybersource_reference_number',
                'cybersource_auth_code',
                'cybersource_decision',
                'cybersource_reason_code',
                'cybersource_payment_token'
            ]);
        });
    }
};

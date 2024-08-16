<template>
        <items-form
                :recordId="recordId"
                :showDialog.sync="showDialog"
                :isReadonly="true"
            ></items-form>

</template>

<script>
import ItemsForm from "../../tenant/items/form.vue";
    export default {
        props: ["recordId","configuration"],
        components: {ItemsForm },
        data() {
            return {
                showDialog:true,
                record: {},
                loading: false,
                errors: {}
            };
        },
          
        
    }
</script>
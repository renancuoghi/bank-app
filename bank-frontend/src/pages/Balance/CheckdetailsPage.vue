<template>
  <q-page>
    <CheckDetailForm />
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import CheckDetailForm from 'src/components/Balance/Form/Check/CheckDetailForm.vue';

export default defineComponent({
  name: 'CheckdetailsPage',

  components: {
    CheckDetailForm,
  },
});
</script>

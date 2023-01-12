<template>
  <q-page>
    <div class="q-pa-md bg-cyan text-white">
      <div class="row">
        <div class="col">
          <BalanceTotalComponent />
        </div>
      </div>
    </div>
    <CheckdepositForm />
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import CheckdepositForm from 'src/components/Balance/Form/Check/CheckdepositForm.vue';
import BalanceTotalComponent from 'src/components/Balance/Transaction/BalanceTotalComponent.vue';

export default defineComponent({
  name: 'CheckdepositPage',

  components: {
    CheckdepositForm,
    BalanceTotalComponent,
  },
});
</script>

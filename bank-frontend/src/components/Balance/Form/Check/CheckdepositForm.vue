<template>
  <div class="q-pa-md">
    <q-form class="q-gutter-md">
      <q-input
        outlined
        v-model="state.amount"
        type="number"
        label="AMOUNT"
        prefix="$"
      />

      <q-input v-model="state.description" label="DESCRIPTION" />

      <div>
        <q-file
          name="poster_file"
          :model-value="state.uploadedImage"
          filled
          label="Upload image check"
          @update:model-value="loadImage"
          v-if="state.uploadShow"
        />
        <q-img :src="state.image" v-if="!state.uploadShow" :fit="'fill'" />
      </div>

      <div class="fixed-bottom" style="margin: 15px">
        <q-btn
          label="DEPOSIT CHECK"
          class="register-button"
          type="button"
          color="primary"
          :size="'lg'"
          @click="onSubmit"
        />
      </div>
    </q-form>
  </div>
</template>

<script lang="ts">
import { useQuasar } from 'quasar';
import TransactionApi from 'src/api/transaction/transactionapi';
import {
  ITransaction,
  TransactionType,
} from 'src/models/balance/balancetransaction';
import { IResponseSuccess } from 'src/models/shared/baseinterfaces';
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'CheckdepositForm',
  setup() {
    const $q = useQuasar();

    const state = ref({
      amount: 0,
      description: '',
      image: '',
      uploadShow: true,
      uploadedImage: null,
    });

    function cleanState() {
      state.value = {
        amount: 0,
        description: '',
        image: '',
        uploadShow: false,
        uploadedImage: null,
      };
    }

    async function createBase64(
      image: File,
      callback: (base64: string) => void
    ) {
      const reader = new FileReader();
      reader.onload = (ev: ProgressEvent<FileReader>) => {
        callback(ev.target?.result as string);
      };
      console.log(image);
      reader.readAsDataURL(image);
    }

    async function loadImage(fileUploaded: File) {
      await createBase64(fileUploaded, (base64: string) => {
        state.value.image = base64;
        state.value.uploadShow = false;
      });
    }

    async function onSubmit() {
      const transactionApi = new TransactionApi();
      transactionApi.create(
        {
          amount: state.value.amount,
          description: state.value.description,
          image: state.value.image,
          transaction_type: TransactionType.CREDIT,
          created_at: null,
        },
        (response: IResponseSuccess<ITransaction>) => {
          cleanState();
          $q.notify({
            message: response.message,
            color: 'positive',
          });
        },
        (error: string) => {
          $q.notify({
            message: error,
            color: 'negative',
          });
        }
      );
    }

    return {
      state,
      onSubmit,
      loadImage,
    };
  },
});
</script>
<style lang="scss" scoped>
.register-button {
  width: 100%;
}
</style>

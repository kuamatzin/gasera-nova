import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-file-esteroids', IndexField)
  app.component('detail-file-esteroids', DetailField)
  app.component('form-file-esteroids', FormField)
})

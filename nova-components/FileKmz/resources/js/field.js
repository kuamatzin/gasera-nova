import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-file-kmz', IndexField)
  app.component('detail-file-kmz', DetailField)
  app.component('form-file-kmz', FormField)
})

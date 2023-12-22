import { Editor } from '@tinymce/tinymce-react'
import { Editor as EditorType, EditorManager } from 'tinymce'
import { useRef, useState } from 'react'

// These files do exist and are the only ones we want to use raw-loader on
// so we disable the rules for these lines
/* eslint-disable import/no-webpack-loader-syntax */
/* @ts-ignore */
import contentCss from '!!raw-loader!tinymce/skins/content/default/content.min.css'
/* @ts-ignore */
import contentUiCss from '!!raw-loader!tinymce/skins/ui/oxide/content.min.css'
/* @ts-ignore */
import customCss from '!!raw-loader!./style.css'
/* @ts-ignore */
import styleCss from './style.css'
/* eslint-enable import/no-webpack-loader-syntax */

let tinymce: EditorManager

if (typeof window !== 'undefined') {
  tinymce = require('tinymce/tinymce')
  require('tinymce/models/dom')

  require('tinymce/themes/silver')
  require('tinymce/icons/default')
  require('tinymce/skins/ui/oxide/skin.min.css')

  require('tinymce/plugins/link')
  require('tinymce/plugins/image')
  require('tinymce/plugins/lists')
}

type PropTypes = {
  content?: string
  onChange?: (content: string) => void
  className?: string
  height?: number
  width?: number
}

const HtmlEditor = ({ content, onChange = () => {}, className, height, width }: PropTypes) => {
  const editorRef = useRef<EditorType|null>(null)
  const [hasEdited, setHasEdited] = useState(false)

  const handleChange = (value: string) => {
    if (!hasEdited) {
      setHasEdited(true)
    }

    onChange(value)
  }

  return (
    <div className={className}>
      <Editor
        onInit={(_, editor) => editorRef.current = editor}
        initialValue={content}
        onEditorChange={handleChange}
        init={{
          promotion: false,
          branding: false,
          skin: false,
          max_height: height || 1000,
          height: 900,
          max_width: width || 200,
          content_css: false,
          content_style: [contentCss, contentUiCss, customCss, styleCss].join('\n'),
          menubar: false,
          statusbar: false,
          plugins: ['lists', 'link', 'image'],
          toolbar: 'placeholders | underline bold italic superscript subscript alignleft aligncenter alignright alignjustify | bullist numlist | link image',

          noneditable_class: 'placeholder_tag',

          file_picker_types: 'image',
          file_picker_callback: (cb) => {
            const input = document.createElement('input')
            input.setAttribute('type', 'file')
            input.setAttribute('accept', 'image/*')

            input.addEventListener('change', (e) => {
              const target = e.target as HTMLInputElement
              const file = target.files![0]

              const reader = new FileReader()
              reader.addEventListener('load', () => {
                /*
                  Note: Now we need to register the blob in TinyMCEs image blob
                  registry. In the next release this part hopefully won't be
                  necessary, as we are looking to handle it internally.
                */
                const id = 'blobid' + (new Date()).getTime()
                const blobCache = tinymce.activeEditor!.editorUpload.blobCache
                const base64 = reader.result!.toString().split(',')[1]
                const blobInfo = blobCache.create(id, file, base64)
                blobCache.add(blobInfo)

                /* call the callback and populate the Title field with the file name */
                cb(blobInfo.blobUri(), { title: file.name })
              })
              reader.readAsDataURL(file)
            })

            input.click()
          },

          setup (editor) {
            editor.ui.registry.addMenuButton('placeholders', {
              text: 'Dynamic Fields',
              type: 'menubutton',
              fetch (success) {
                // TinyMCE doesn't export the type for some reason
                const items: Record<string, string|(() => void)>[] = [
                  {
                    type: 'nestedmenuitem',
                    text: 'Client',
                    getSubmenuItems: () => [
                      {
                        type: 'menuitem',
                        text: 'Client Full Name',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ user.full_name }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Title',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ user.title }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Client First Name',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ user.first_name }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Client Last Name',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ user.last_name }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Suffix',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ user.suffix }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Client Email',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ user.email }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Client Phone',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ user.phone }}</span>'),
                      },
                    ],
                  },
                  {
                    type: 'nestedmenuitem',
                    text: 'Conveyancer',
                    getSubmenuItems: () => [
                      {
                        type: 'menuitem',
                        text: 'Fee earner name',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.name }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Fee earner email',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.email_address }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Fee earner SRA/CLC Number',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.sra_clc_number }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Fee earner direct dial phone number',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.telephone_number }}</span>'),
                      },
                    ],
                  },
                  {
                    type: 'nestedmenuitem',
                    text: 'Company',
                    getSubmenuItems: () => [
                      {
                        type: 'menuitem',
                        text: 'Company Name',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.name }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Branch address',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.address }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Branch phone number',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.telephone_number }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Website address',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.website }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Company SRA/CLC Number',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.sra_clc_number }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'VAT number',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.vat_number }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Company number',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ conveyancer.company_number }}</span>'),
                      },
                    ],
                  },
                  {
                    type: 'nestedmenuitem',
                    text: 'Property',
                    getSubmenuItems: () => [
                      {
                        type: 'menuitem',
                        text: 'Case Reference',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ property.case_reference }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Payment on account amount',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ property.payment_on_account_amount }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Conveyancing Fee',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ property.conveyancing_fee }}</span>'),
                      },
                      {
                        type: 'menuitem',
                        text: 'Sale Price',
                        onAction: () => editor.insertContent('<span class="placeholder_tag">{{ property.sale_price }}</span>'),
                      },
                      {
                        type: 'nestedmenuitem',
                        text: 'Address',
                        getSubmenuItems: () => [
                          {
                            type: 'menuitem',
                            text: 'Full address',
                            onAction: () => editor.insertContent('<span class="placeholder_tag">{{ property.address.single_line }}</span>'),
                          },
                          {
                            type: 'menuitem',
                            text: 'Line 1',
                            onAction: () => editor.insertContent('<span class="placeholder_tag">{{ property.address.line_1 }}</span>'),
                          },
                          {
                            type: 'menuitem',
                            text: 'Line 2',
                            onAction: () => editor.insertContent('<span class="placeholder_tag">{{ property.address.line_2 }}</span>'),
                          },
                          {
                            type: 'menuitem',
                            text: 'City',
                            onAction: () => editor.insertContent('<span class="placeholder_tag">{{ property.address.city }}</span>'),
                          },
                          {
                            type: 'menuitem',
                            text: 'Postcode',
                            onAction: () => editor.insertContent('<span class="placeholder_tag">{{ property.address.postcode }}</span>'),
                          },
                        ],
                      },
                    ],
                  },
                ]
                success(items)
              },
            })
          },
        }}
      />
    </div>
  )
}

export default HtmlEditor

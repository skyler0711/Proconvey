import DataEntryList from '@proconvey/ui/src/components/DataEntryList'
import Form from '@proconvey/ui/src/components/Form'
import Checkbox from '@proconvey/ui/src/components/Form/Checkbox'
import Input from '@proconvey/ui/src/components/Form/Input'
import { H2 } from '@proconvey/ui/src/components/Headers'
import { Answer, AnswerDetailsDataTable, AnswerType } from 'gql/graphql'
import React, { useEffect } from 'react'
import { FieldError } from 'react-hook-form'

export const getDataTableType = (
  type: AnswerType,
  value: any,
  onChange: Function,
  name: string,
  isExcludedChecked: boolean = false,
  placeholder?: string,
): JSX.Element => {
  switch (type) {
    case AnswerType.Text:
      return (
        <div>
          <Input
            type="text"
            onChange={e => onChange(e.target.value)}
            placeholder={placeholder}
            value={value}
            disabled={(!isExcludedChecked && name === 'Price')}
          />
        </div>
      )
    case AnswerType.Checkbox:
      return (
        <div className="flex flex-col gap-7">
          <Checkbox.Group>
            {() => (
              <Checkbox
                value={value}
                rounded
                size="small"
                selected={value !== '1' ? [] : [value]}
                onChange={() => {
                  onChange(value === '1' ? null : '1')
                }}
              >
              </Checkbox>
            )}
          </Checkbox.Group>
        </div>
      )
    case AnswerType.Address:
    case AnswerType.DataTable:
    case AnswerType.Dropdown:
    case AnswerType.File:
    case AnswerType.MultiSelect:
    case AnswerType.Number:
    case AnswerType.OwnerDropdown:
    case AnswerType.SingleSelect:
    case AnswerType.Textarea:
    case AnswerType.PersonMultiSelect:
    default:
      return (<></>)
  }
}

type PropTypes = {
  answer: Answer
  value: any
  onChange: (value: any, column: number) => void
  errors?: { columns: FieldError[] }
}

const DataTable = ({ answer, value, onChange, errors }: PropTypes) => {
  const ExcludedColumnIndex = 1

  const answerDetail = answer.details as AnswerDetailsDataTable

  const handleChange = (changeValue: any, row: number, column: number, isAdditional: boolean = false) => {
    let lastKey = Math.max(...Object.keys(value?.columns ?? {}).map(e => parseInt(e)))
    let isCheckbox = (answerDetail?.columns?.[column]?.type as AnswerType ?? AnswerType.Text) === AnswerType.Checkbox

    let newRow = { ...(value?.columns?.[row] ?? {}), isAdditional: isAdditional, [column]: changeValue }

    if (!isAdditional && isCheckbox) answerDetail.columns.forEach((e, i) => {
      if (e?.type === AnswerType.Checkbox && column !== i) newRow[i] = '0'
    })

    if (isAdditional && isCheckbox) {
      answerDetail.columns.forEach((e, i) => {
        if (e?.type === AnswerType.Checkbox && column !== i) {
          newRow[i] = '0'
        }
      })
    }

    changeValue = { ...value, columns: { ...(value?.columns ?? {}), [row]: newRow }, rows: [true] }

    // if additional row is empty, delete it
    let isEmpty = Object.entries(newRow).filter(e => e[0] !== 'isAdditional').every(e => !e[1])
    if (isEmpty && isAdditional) delete changeValue.columns[row]

    onChange(changeValue, row)
    if (isAdditional && row === lastKey) addField() // if last additional row, add new row
  }

  const addField = () => {
    let rowIndex = Math.max(...Object.keys(value?.columns ?? {}).filter(e => value.columns[e].isAdditional).map(e => parseInt(e)), answerDetail.rows.length)

    let changeValue = {
      ...value,
      columns: {
        ...(value?.columns ?? {}),
        [rowIndex + 1]: {
          ...Array(answerDetail.columns.length + 1).fill(null),
          isAdditional: true,
        },
      },
      rows: { ...(value?.rows ?? {}), [rowIndex]: value?.rows?.[rowIndex] ?? {} },
    }

    onChange(changeValue, rowIndex + 1)
  }

  const getCurrentAnswer = (rowIndex?: number, columnIndex?: number) => {
    let lastKey = Math.max(...Object.keys(value?.columns ?? {}).filter(e => value.columns[e].isAdditional).map(e => parseInt(e)), answerDetail.rows.length)
    if (answerDetail?.allowsAddMore && rowIndex === lastKey) return '' // if last row, return empty string

    return value?.columns?.[rowIndex!]?.[columnIndex!]
  }

  useEffect(() => {
    if (!answerDetail?.allowsAddMore) return
    if (Object.keys(value?.columns ?? {}).filter(e => value?.columns?.[e].isAdditional).length > 0) return
    addField() // Add first additional field
  })

  return (
    <DataEntryList>
      <DataEntryList.Head>
        <DataEntryList.Row>
          <DataEntryList.Cell as="th" customStyle="w-[30%]">Item</DataEntryList.Cell>
          {answerDetail?.columns?.map((column, index) => (
            <DataEntryList.Cell as="th" key={index + answer.id} align={index <= 2 ? 'center' : 'left'} customStyle={(answerDetail?.columns?.length === 7 && index === 3) ? ' border-l-2 border-primary border-opacity-20' : ''}>{column.name}</DataEntryList.Cell>
          ))}
        </DataEntryList.Row>
      </DataEntryList.Head>
      {answerDetail?.rows?.map((row, rIndex) => (
        <DataEntryList.Row key={rIndex + answer.id}>
          <DataEntryList.Cell hasError={!!errors?.columns?.[rIndex]?.message}>{row.name}</DataEntryList.Cell>
          {answerDetail?.columns?.map((column, cIndex) => (
            <DataEntryList.Cell customStyle={(answerDetail?.columns?.length === 7 && cIndex === 3) ? 'border-l-2 border-primary border-opacity-20' : ''} key={cIndex + answer.id} hasError={!!errors?.columns?.[rIndex]?.message}>
              {getDataTableType(
                column.type,
                getCurrentAnswer(rIndex, cIndex),
                (value: any) => handleChange(value, rIndex, cIndex),
                column.name,
                value?.columns?.[rIndex]?.[ExcludedColumnIndex] === '1',
                column.placeholder ?? '',
              )}
            </DataEntryList.Cell>
          ))}
        </DataEntryList.Row>
      ))}
      {
        answerDetail?.allowsAddMore &&
        <>
          {
            answerDetail?.addMoreLabel &&
            <H2 className="w-full whitespace-nowrap">{answerDetail.addMoreLabel}</H2>
          }
          {
            Object.keys(value?.columns ?? {}).filter(e => value?.columns?.[e]?.isAdditional).map((rIndex) => (
              <DataEntryList.Row key={rIndex}>
                <DataEntryList.Cell><Form.Input className="font-normal" placeholder="Item name" /></DataEntryList.Cell>
                {Object.keys(value?.columns?.[rIndex] ?? {}).filter(e => e !== 'isAdditional').map((column: any, cIndex: number) => (
                  <DataEntryList.Cell customStyle={(answerDetail?.columns?.length === 7 && cIndex === 3) ? 'border-l-2 border-primary border-opacity-20' : ''} key={cIndex + answer.id}>
                    {getDataTableType(
                      answerDetail?.columns[cIndex]?.type as AnswerType,
                      getCurrentAnswer(Number.parseInt(rIndex), Number.parseInt(column)),
                      (e: string) => handleChange(e, Number.parseInt(rIndex), Number.parseInt(column), true),
                      answerDetail?.columns[cIndex]?.name,
                      value?.columns?.[rIndex]?.[ExcludedColumnIndex] === '1',
                      answerDetail?.columns[cIndex]?.placeholder ?? '',
                    )}
                  </DataEntryList.Cell>
                ))}
              </DataEntryList.Row>
            ))
          }
        </>
      }
    </DataEntryList>
  )
}


export default DataTable

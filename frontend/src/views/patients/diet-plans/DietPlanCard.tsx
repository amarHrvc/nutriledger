'use client'

import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import Stack from '@mui/material/Stack'
import Table from '@mui/material/Table'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import TableContainer from '@mui/material/TableContainer'
import TableHead from '@mui/material/TableHead'
import TableRow from '@mui/material/TableRow'
import Tooltip from '@mui/material/Tooltip'
import Typography from '@mui/material/Typography'

import type { DietPlan } from './types'

interface Props {
  plan: DietPlan
  onRegenerate: () => void
}

export default function DietPlanCard({ plan, onRegenerate }: Props) {
  const days = plan.days ?? []
  const goals = plan.nutritionalGoals
  const warnings = plan.warnings ?? []

  return (
    <Card variant='outlined'>
      <CardContent>
        {plan.rationale && (
          <Typography variant='body2' color='text.secondary' sx={{ fontStyle: 'italic', mb: 2 }}>
            {plan.rationale}
          </Typography>
        )}

        <Stack direction='row' spacing={3} alignItems='center' sx={{ mb: 2 }}>
          <Typography variant='h6' fontWeight={700}>
            {plan.dailyCalories} kcal/day
          </Typography>
          {goals && (
            <>
              <Typography variant='body2'>Protein: <strong>{goals.protein_g}g</strong></Typography>
              <Typography variant='body2'>Carbs: <strong>{goals.carbs_g}g</strong></Typography>
              <Typography variant='body2'>Fat: <strong>{goals.fat_g}g</strong></Typography>
            </>
          )}
        </Stack>

        {warnings.length > 0 && (
          <Stack direction='row' spacing={1} flexWrap='wrap' sx={{ mb: 2 }}>
            {warnings.map((w, i) => (
              <Chip key={i} label={w} color='warning' size='small' />
            ))}
          </Stack>
        )}

        <TableContainer>
          <Table size='small'>
            <TableHead>
              <TableRow sx={{ backgroundColor: 'action.hover' }}>
                <TableCell><strong>Day</strong></TableCell>
                <TableCell><strong>Breakfast</strong></TableCell>
                <TableCell><strong>Lunch</strong></TableCell>
                <TableCell><strong>Dinner</strong></TableCell>
                <TableCell><strong>Snack</strong></TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {days.map((day, i) => (
                <TableRow key={i}>
                  <TableCell><strong>{day.day}</strong></TableCell>
                  <TableCell>{day.breakfast}</TableCell>
                  <TableCell>{day.lunch}</TableCell>
                  <TableCell>{day.dinner}</TableCell>
                  <TableCell>{day.snack}</TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>

        <Box sx={{ mt: 2, display: 'flex', gap: 1 }}>
          <Button variant='outlined' onClick={onRegenerate}>
            Regenerate
          </Button>
          <Tooltip title='Coming soon'>
            <span>
              <Button variant='outlined' disabled>
                Export PDF
              </Button>
            </span>
          </Tooltip>
        </Box>
      </CardContent>
    </Card>
  )
}

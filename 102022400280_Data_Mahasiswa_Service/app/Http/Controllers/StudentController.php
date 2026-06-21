<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Students",
    description: "Student Service API"
)]
class StudentController extends Controller
{
    #[OA\Get(
        path: "/api/v1/students",
        summary: "Get all students",
        tags: ["Students"],
        security: [["ApiKeyAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success")
        ]
    )]
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'List mahasiswa berhasil diambil',
            'data' => Student::all()
        ], 200);
    }

    #[OA\Get(
        path: "/api/v1/students/{id}",
        summary: "Get student detail by ID",
        tags: ["Students"],
        security: [["ApiKeyAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Success"),
            new OA\Response(response: 404, description: "Student not found")
        ]
    )]
    public function show($id)
    {
        $student = Student::where('id', $id)->orWhere('nim', $id)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Mahasiswa tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Detail mahasiswa berhasil diambil',
            'data' => $student
        ], 200);
    }

    #[OA\Post(
        path: "/api/v1/students/validate-quota",
        summary: "Validate student quota",
        tags: ["Students"],
        security: [["ApiKeyAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["student_id", "requested_sks"],
                properties: [
                    new OA\Property(
                        property: "student_id",
                        type: "integer",
                        example: 1
                    ),
                    new OA\Property(
                        property: "requested_sks",
                        type: "integer",
                        example: 4
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Validation success"),
            new OA\Response(response: 400, description: "Validation failed"),
            new OA\Response(response: 404, description: "Student not found")
        ]
    )]
    public function validateQuota(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required',
            'requested_sks' => 'required|integer|min:1'
        ]);

        $student = Student::where('id', $validated['student_id'])->orWhere('nim', $validated['student_id'])->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Mahasiswa tidak ditemukan',
                'data' => null
            ], 404);
        }

        if ($student->status !== 'AKTIF') {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Mahasiswa tidak aktif',
                'data' => null
            ], 400);
        }

        $remaining = $student->quota_sks - $student->used_sks;

        if ($validated['requested_sks'] > $remaining) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Kuota SKS tidak mencukupi',
                'data' => [
                    'student_id' => $student->id,
                    'remaining_quota' => $remaining,
                    'requested_sks' => $validated['requested_sks'],
                    'eligible' => false
                ]
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Kuota SKS cukup',
            'data' => [
                'student_id' => $student->id,
                'remaining_quota' => $remaining,
                'requested_sks' => $validated['requested_sks'],
                'eligible' => true
            ]
        ], 200);
    }
}

